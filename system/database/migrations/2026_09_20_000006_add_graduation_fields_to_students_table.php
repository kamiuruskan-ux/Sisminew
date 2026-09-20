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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'graduation_year')) {
                $table->string('graduation_year', 20)->nullable()->after('entry_year');
            }
            if (!Schema::hasColumn('students', 'alumni_notes')) {
                $table->text('alumni_notes')->nullable()->after('graduation_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'graduation_year')) {
                $table->dropColumn('graduation_year');
            }
            if (Schema::hasColumn('students', 'alumni_notes')) {
                $table->dropColumn('alumni_notes');
            }
        });
    }
};
