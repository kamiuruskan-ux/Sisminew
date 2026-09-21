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
        if (!Schema::hasTable('quran_halaqah_members')) {
            Schema::create('quran_halaqah_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teacher_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->integer('grade')->nullable();
                $table->string('group_name', 100)->nullable();
                $table->timestamps();

                $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();

                $table->index(['teacher_id', 'grade']);
                $table->index(['student_id', 'academic_year_id']);
                $table->index('grade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_halaqah_members');
    }
};
