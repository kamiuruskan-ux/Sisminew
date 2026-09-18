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
        Schema::create('canteen_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('canteen_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('canteen_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(100);
            $table->string('image')->nullable();
            $table->string('stall_name')->default('Kantin Utama');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('canteen_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method')->default('savings_balance'); // savings_balance, qris, cash
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
            $table->string('order_status')->default('pending'); // pending, processing, ready, completed, cancelled
            $table->string('qr_code')->unique();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('canteen_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('canteen_order_id')->constrained('canteen_orders')->onDelete('cascade');
            $table->foreignId('canteen_item_id')->nullable()->constrained('canteen_items')->onDelete('set null');
            $table->string('item_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canteen_order_items');
        Schema::dropIfExists('canteen_orders');
        Schema::dropIfExists('canteen_items');
        Schema::dropIfExists('canteen_categories');
    }
};
