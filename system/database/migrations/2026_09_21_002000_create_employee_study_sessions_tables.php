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
        if (!Schema::hasTable('employee_study_sessions')) {
            Schema::create('employee_study_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('speaker');
                $table->date('date');
                $table->string('time_start', 10)->nullable();
                $table->string('time_end', 10)->nullable();
                $table->string('location')->default('Masjid SDIT Al-Fahmi');
                $table->text('material_summary')->nullable();
                $table->string('attachment_path')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->index(['date']);
            });
        }

        if (!Schema::hasTable('employee_study_attendances')) {
            Schema::create('employee_study_attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id');
                $table->unsignedBigInteger('user_id');
                $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
                $table->string('notes', 255)->nullable();
                $table->timestamps();

                $table->foreign('session_id')->references('id')->on('employee_study_sessions')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['session_id', 'user_id']);
                $table->index(['session_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_study_attendances');
        Schema::dropIfExists('employee_study_sessions');
    }
};
