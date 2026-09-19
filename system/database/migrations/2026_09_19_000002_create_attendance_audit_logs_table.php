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
        if (!Schema::hasTable('attendance_audit_logs')) {
            Schema::create('attendance_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('teacher_attendance_id')->nullable()->constrained('teacher_attendances')->nullOnDelete();
                $table->string('action', 50); // check_in, check_out, briefing, afternoon_session, lock_rejected, etc.
                $table->string('method', 50); // mobile_gps, fingerprint, face_id, manual
                $table->string('status', 30); // success, rejected, warning, error
                $table->string('session_type', 30)->nullable(); // morning, briefing, afternoon, evening
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device_info', 255)->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->integer('distance_meters')->nullable();
                $table->json('details')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['action', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_audit_logs');
    }
};
