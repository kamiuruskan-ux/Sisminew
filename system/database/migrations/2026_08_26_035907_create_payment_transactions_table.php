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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference_type'); // 'spmb' or 'spp'
            $table->unsignedBigInteger('reference_id'); // ID of spmb_registrations or student_payment_details
            $table->string('payment_gateway'); // 'midtrans', 'tripay', 'manual'
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 14, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'expired'])->default('pending');
            $table->string('snap_token')->nullable();
            $table->text('payment_url')->nullable();
            $table->string('payment_method_code')->nullable(); // e.g. BCAVA, QRIS
            $table->json('payload')->nullable();
            $table->string('payment_proof')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
