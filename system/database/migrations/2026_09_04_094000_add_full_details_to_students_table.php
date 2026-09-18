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
            if (!Schema::hasColumn('students', 'nis')) {
                $table->string('nis')->nullable()->after('nisn');
            }
            if (!Schema::hasColumn('students', 'religion')) {
                $table->string('religion')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('students', 'student_status')) {
                $table->string('student_status')->default('active')->after('class_id');
            }
            if (!Schema::hasColumn('students', 'entry_year')) {
                $table->string('entry_year', 20)->nullable()->after('student_status');
            }
            if (!Schema::hasColumn('students', 'father_name')) {
                $table->string('father_name')->nullable()->after('parent_name');
            }
            if (!Schema::hasColumn('students', 'mother_name')) {
                $table->string('mother_name')->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('students', 'parent_job')) {
                $table->string('parent_job')->nullable()->after('parent_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['nis', 'religion', 'student_status', 'entry_year', 'father_name', 'mother_name', 'parent_job']);
        });
    }
};
