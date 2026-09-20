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
        if (!Schema::hasTable('teaching_agendas')) {
            Schema::create('teaching_agendas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teacher_id');
                $table->unsignedBigInteger('class_id');
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->string('subject', 150);
                $table->date('date');
                $table->text('material_taught');
                $table->text('class_notes')->nullable();
                $table->timestamps();

                $table->index(['teacher_id', 'date']);
                $table->index(['class_id', 'subject']);
            });
        }

        if (!Schema::hasTable('teaching_agenda_students')) {
            Schema::create('teaching_agenda_students', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teaching_agenda_id');
                $table->unsignedBigInteger('student_id');
                $table->string('attendance_status', 20)->default('hadir');
                $table->decimal('score_cognitive', 5, 2)->default(80);
                $table->decimal('score_adab', 5, 2)->default(80);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('teaching_agenda_id')->references('id')->on('teaching_agendas')->onDelete('cascade');
                $table->index(['student_id', 'teaching_agenda_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_agenda_students');
        Schema::dropIfExists('teaching_agendas');
    }
};
