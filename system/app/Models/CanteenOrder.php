<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CanteenOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'student_id',
        'cashier_id',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'qr_code',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'KTN-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            if (empty($order->qr_code)) {
                $order->qr_code = 'KTNQR-' . date('YmdHis') . '-' . strtoupper(Str::random(8));
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CanteenOrderItem::class, 'canteen_order_id');
    }

    public function getOrderStatusBadgeAttribute(): string
    {
        return match ($this->order_status) {
            'pending' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Menunggu</span>',
            'processing' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Diproses</span>',
            'ready' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">Siap Diambil</span>',
            'completed' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Selesai</span>',
            'cancelled' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">Dibatalkan</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">' . ucfirst($this->order_status) . '</span>',
        };
    }

    public function getPaymentStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-500 text-white">Lunas</span>',
            'unpaid' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-500 text-white">Belum Bayar</span>',
            'refunded' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-500 text-white">Refund</span>',
            default => ucfirst($this->payment_status),
        };
    }
}
