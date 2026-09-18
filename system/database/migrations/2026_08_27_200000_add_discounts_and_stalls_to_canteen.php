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
        Schema::create('canteen_stalls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('owner_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('banner')->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('operating_hours')->default('07:00 - 15:00 WIB');
            $table->decimal('rating', 3, 1)->default(4.8);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('canteen_items', function (Blueprint $table) {
            $table->foreignId('stall_id')->nullable()->after('category_id')->constrained('canteen_stalls')->onDelete('set null');
            $table->integer('discount_percent')->default(0)->after('price');
            $table->decimal('original_price', 12, 2)->nullable()->after('discount_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canteen_items', function (Blueprint $table) {
            $table->dropForeign(['stall_id']);
            $table->dropColumn(['stall_id', 'discount_percent', 'original_price']);
        });

        Schema::dropIfExists('canteen_stalls');
    }
};
