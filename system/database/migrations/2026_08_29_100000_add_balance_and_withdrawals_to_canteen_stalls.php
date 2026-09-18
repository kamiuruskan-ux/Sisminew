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
        // 1. Add fields to canteen_stalls
        Schema::table('canteen_stalls', function (Blueprint $table) {
            if (!Schema::hasColumn('canteen_stalls', 'balance')) {
                $table->decimal('balance', 15, 2)->default(0.00)->after('description');
            }
            if (!Schema::hasColumn('canteen_stalls', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('balance');
            }
            if (!Schema::hasColumn('canteen_stalls', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('canteen_stalls', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_account_number');
            }
        });

        // 2. Create canteen_withdrawals table
        Schema::create('canteen_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stall_id')->constrained('canteen_stalls')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_name');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 3. Create canteen_transactions table
        Schema::create('canteen_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stall_id')->constrained('canteen_stalls')->onDelete('cascade');
            $table->string('type'); // income, withdraw, refund
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_no')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canteen_transactions');
        Schema::dropIfExists('canteen_withdrawals');
        Schema::table('canteen_stalls', function (Blueprint $table) {
            $table->dropColumn(['balance', 'bank_name', 'bank_account_number', 'bank_account_name']);
        });
    }
};
