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
        if (!Schema::hasTable('school_feedbacks')) {
            Schema::create('school_feedbacks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('category', 20)->default('saran'); // 'saran', 'kritik', 'masukan'
                $table->text('content');
                $table->boolean('is_anonymous')->default(false);
                $table->string('author_name')->nullable();
                $table->string('author_role')->nullable();
                $table->string('status', 20)->default('baru'); // 'baru', 'dibahas', 'selesai'
                $table->text('admin_notes')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->index(['category', 'status']);
                $table->index(['created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_feedbacks');
    }
};
