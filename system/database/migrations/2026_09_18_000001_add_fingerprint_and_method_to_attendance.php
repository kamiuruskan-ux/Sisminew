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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'fingerprint_template')) {
                $table->longText('fingerprint_template')->nullable()->after('face_registered_at');
            }
            if (!Schema::hasColumn('users', 'fingerprint_registered_at')) {
                $table->timestamp('fingerprint_registered_at')->nullable()->after('fingerprint_template');
            }
        });

        Schema::table('teacher_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_attendances', 'method')) {
                $table->string('method', 30)->default('manual')->after('status'); // 'fingerprint', 'mobile_gps', 'face_id', 'manual'
            }
            if (!Schema::hasColumn('teacher_attendances', 'device_info')) {
                $table->string('device_info', 255)->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'fingerprint_template')) {
                $table->dropColumn(['fingerprint_template', 'fingerprint_registered_at']);
            }
        });

        Schema::table('teacher_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_attendances', 'method')) {
                $table->dropColumn(['method', 'device_info']);
            }
        });
    }
};
