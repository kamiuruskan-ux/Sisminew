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
        Schema::table('payment_bills', function (Blueprint $table) {
            $table->string('target_type')->default('all')->after('academic_year_id');
            $table->foreignId('major_id')->nullable()->constrained('majors')->onDelete('set null')->after('class_id');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null')->after('major_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_bills', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['major_id']);
            $table->dropColumn(['target_type', 'major_id', 'student_id']);
        });
    }
};
