<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomNotification extends Model
{
    protected $table = 'custom_notifications';

    protected $fillable = [
        'sender_id',
        'title',
        'message',
        'type',
        'target_type',
        'target_payload',
        'action_url',
        'channels',
        'sent_count',
        'read_count',
        'status',
    ];

    protected $casts = [
        'target_payload' => 'array',
        'channels' => 'array',
        'sent_count' => 'integer',
        'read_count' => 'integer',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AppNotification::class, 'custom_notification_id');
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'warning' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400',
            'urgent' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400',
            'attendance_reminder' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400',
            'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400',
            default => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'warning' => 'Peringatan',
            'urgent' => 'Penting / Mendesak',
            'attendance_reminder' => 'Pengingat Presensi',
            'success' => 'Sukses',
            default => 'Informasi',
        };
    }

    /**
     * Pastikan tabel custom_notifications ada di database
     */
    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('custom_notifications')) {
            Schema::create('custom_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sender_id')->nullable()->index();
                $table->string('title', 255);
                $table->text('message');
                $table->string('type', 50)->default('info')->index();
                $table->string('target_type', 50)->default('all')->index();
                $table->json('target_payload')->nullable();
                $table->string('action_url', 255)->nullable();
                $table->json('channels')->nullable();
                $table->integer('sent_count')->default(0);
                $table->integer('read_count')->default(0);
                $table->string('status', 30)->default('sent')->index();
                $table->timestamps();
            });
        }
    }
}
