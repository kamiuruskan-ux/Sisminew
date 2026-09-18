<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_topic_quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('lms_topic_quiz_id')->constrained('lms_topic_quizzes')->cascadeOnDelete();
            $table->string('selected_option', 10);
            $table->boolean('is_correct')->default(false);
            $table->integer('awarded_xp')->default(0);
            $table->timestamps();

            $table->unique(['student_id', 'lms_topic_quiz_id'], 'student_quiz_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_topic_quiz_answers');
    }
};
