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
            if (!Schema::hasColumn('teacher_attendances', 'delay_minutes')) {
                $table->integer('delay_minutes')->nullable()->after('status');
            }
        });

        // Ensure status column can store 'very_late', 'outside_window', 'present', 'late', etc.
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `teacher_attendances` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'present'");
        } catch (\Throwable $e) {
            // Ignore if already varchar or non-mysql
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_attendances', 'delay_minutes')) {
                $table->dropColumn('delay_minutes');
            }
        });
    }
};
