<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PushSubscription extends Model
{
    protected $table = 'push_subscriptions';

    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
        'device_type',
        'user_agent',
        'is_active',
        'last_active_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Pastikan tabel push_subscriptions ada di database
     */
    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('push_subscriptions')) {
            Schema::create('push_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('endpoint', 500)->unique();
                $table->text('public_key')->nullable();
                $table->string('auth_token', 255)->nullable();
                $table->string('content_encoding', 50)->default('aes128gcm');
                $table->string('device_type', 50)->nullable()->default('desktop');
                $table->text('user_agent')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamp('last_active_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'is_active']);
            });
        }
    }
}
