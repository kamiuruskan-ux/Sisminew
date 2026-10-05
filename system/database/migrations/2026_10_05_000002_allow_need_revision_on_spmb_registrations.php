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
        if (Schema::hasTable('spmb_registrations') && Schema::hasColumn('spmb_registrations', 'status')) {
            try {
                // Change status column to VARCHAR(50) so it accepts 'draft', 'submitted', 'verified', 'accepted', 'rejected', 'need_revision'
                DB::statement("ALTER TABLE `spmb_registrations` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'draft'");
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
        if (Schema::hasTable('spmb_registrations') && Schema::hasColumn('spmb_registrations', 'status')) {
            try {
                DB::statement("ALTER TABLE `spmb_registrations` MODIFY COLUMN `status` ENUM('draft', 'submitted', 'verified', 'accepted', 'rejected') NOT NULL DEFAULT 'draft'");
            } catch (\Throwable $e) {
                //
            }
        }
    }
};
