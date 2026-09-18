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
        Schema::table('canteen_stalls', function (Blueprint $table) {
            if (!Schema::hasColumn('canteen_stalls', 'open_time')) {
                $table->string('open_time', 10)->nullable()->default('07:00')->after('description');
            }
            if (!Schema::hasColumn('canteen_stalls', 'close_time')) {
                $table->string('close_time', 10)->nullable()->default('15:00')->after('open_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canteen_stalls', function (Blueprint $table) {
            $table->dropColumn(['open_time', 'close_time']);
        });
    }
};
