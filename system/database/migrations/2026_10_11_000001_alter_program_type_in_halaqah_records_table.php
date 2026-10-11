<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('halaqah_records') && Schema::hasColumn('halaqah_records', 'program_type')) {
            try {
                // Change program_type column from ENUM('tahsin', 'tahfidz') to VARCHAR(50) so it supports 'tilawah'
                DB::statement("ALTER TABLE `halaqah_records` MODIFY COLUMN `program_type` VARCHAR(50) NOT NULL DEFAULT 'tahsin'");
            } catch (\Throwable $e) {
                // Ignore or log if DB driver doesn't support raw ALTER TABLE statement
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('halaqah_records') && Schema::hasColumn('halaqah_records', 'program_type')) {
            try {
                DB::statement("ALTER TABLE `halaqah_records` MODIFY COLUMN `program_type` ENUM('tahsin', 'tahfidz', 'tilawah') NOT NULL DEFAULT 'tahsin'");
            } catch (\Throwable $e) {
                //
            }
        }
    }
};
