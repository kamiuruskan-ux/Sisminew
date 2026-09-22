<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. push_subscriptions
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

        // 2. custom_notifications
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

        // 3. app_notifications
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('custom_notifications');
        Schema::dropIfExists('push_subscriptions');
    }
};
