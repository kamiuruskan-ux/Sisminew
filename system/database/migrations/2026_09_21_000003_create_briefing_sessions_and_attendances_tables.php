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
        if (!Schema::hasTable('briefing_sessions')) {
            Schema::create('briefing_sessions', function (Blueprint $table) {
                $table->id();
                $table->date('date');
                $table->string('title')->default('Briefing Rutin Harian Pegawai & Evaluasi');
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('opened_by')->nullable();
                $table->string('start_time', 10)->nullable();
                $table->string('end_time', 10)->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->timestamps();

                $table->foreign('opened_by')->references('id')->on('users')->onDelete('set null');
                $table->index(['date', 'is_active']);
            });
        }

        if (!Schema::hasTable('briefing_attendances')) {
            Schema::create('briefing_attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id');
                $table->unsignedBigInteger('user_id');
                $table->date('date');
                $table->string('attended_at', 10)->nullable();
                $table->string('device_info', 255)->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('status', 30)->default('hadir');
                $table->string('notes', 255)->nullable();
                $table->timestamps();

                $table->foreign('session_id')->references('id')->on('briefing_sessions')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['session_id', 'user_id']);
                $table->index(['date', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('briefing_attendances');
        Schema::dropIfExists('briefing_sessions');
    }
};
