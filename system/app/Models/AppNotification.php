<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'custom_notification_id',
        'user_id',
        'title',
        'message',
        'type',
        'category',
        'action_url',
        'is_read',
        'read_at',
        'is_pushed',
        'pushed_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_pushed' => 'boolean',
        'pushed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customNotification(): BelongsTo
    {
        return $this->belongsTo(CustomNotification::class, 'custom_notification_id');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            if ($this->custom_notification_id) {
                CustomNotification::where('id', $this->custom_notification_id)->increment('read_count');
            }
        }
    }

    /**
     * Pastikan tabel app_notifications ada di database
     */
    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('app_notifications')) {
            Schema::create('app_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('custom_notification_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('title', 255);
                $table->text('message');
                $table->string('type', 50)->default('info')->index();
                $table->string('category', 50)->default('general')->index();
                $table->string('action_url', 255)->nullable();
                $table->boolean('is_read')->default(false)->index();
                $table->timestamp('read_at')->nullable();
                $table->boolean('is_pushed')->default(false)->index();
                $table->timestamp('pushed_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'is_read', 'created_at']);
            });
        }
    }
}
