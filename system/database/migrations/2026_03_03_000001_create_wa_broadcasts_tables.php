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
        Schema::create('wa_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('target_type'); // all_students, all_parents, all_teachers, class, spmb, custom
            $table->foreignId('target_class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->text('message');
            $table->enum('status', ['draft', 'processing', 'completed', 'failed'])->default('draft');
            $table->integer('total_recipients')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('wa_broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wa_broadcast_id')->constrained('wa_broadcasts')->cascadeOnDelete();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('response_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_broadcast_logs');
        Schema::dropIfExists('wa_broadcasts');
    }
};
