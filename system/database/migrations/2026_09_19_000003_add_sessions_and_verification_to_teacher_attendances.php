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
        Schema::table('teacher_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_attendances', 'midday_at')) {
                $table->time('midday_at')->nullable()->after('check_in');
            }
            if (!Schema::hasColumn('teacher_attendances', 'session_name')) {
                $table->string('session_name', 50)->nullable()->after('status');
            }
            if (!Schema::hasColumn('teacher_attendances', 'verification_status')) {
                $table->string('verification_status', 30)->default('verified')->after('session_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('teacher_attendances', 'midday_at')) {
                $cols[] = 'midday_at';
            }
            if (Schema::hasColumn('teacher_attendances', 'session_name')) {
                $cols[] = 'session_name';
            }
            if (Schema::hasColumn('teacher_attendances', 'verification_status')) {
                $cols[] = 'verification_status';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
