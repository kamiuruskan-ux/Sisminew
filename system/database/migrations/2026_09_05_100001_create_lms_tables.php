<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Chapters (Bab)
        Schema::create('lms_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('subject');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Topics (Sub-Bab / Video Modul)
        Schema::create('lms_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_chapter_id')->constrained('lms_chapters')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_duration')->default('10:00');
            $table->string('summary_file')->nullable();
            $table->integer('order')->default(0);
            $table->integer('xp_reward')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Quizzes & Pembahasan
        Schema::create('lms_topic_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_topic_id')->constrained('lms_topics')->cascadeOnDelete();
            $table->text('question');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->string('option_e')->nullable();
            $table->enum('correct_option', ['a', 'b', 'c', 'd', 'e']);
            $table->text('explanation')->nullable();
            $table->string('explanation_video')->nullable();
            $table->integer('xp_reward')->default(100);
            $table->timestamps();
        });

        // 4. Progress Belajar Siswa
        Schema::create('lms_topic_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('lms_topic_id')->constrained('lms_topics')->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('watch_seconds')->default(0);
            $table->boolean('quiz_completed')->default(false);
            $table->integer('quiz_score')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'lms_topic_id']);
        });

        // 5. Gamifikasi (XP, Level, Badges, Daily Streak)
        Schema::create('lms_gamifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->integer('xp')->default(0);
            $table->integer('level')->default(1);
            $table->integer('current_streak')->default(0);
            $table->date('last_active_date')->nullable();
            $table->json('badges')->nullable();
            $table->timestamps();
        });

        // 6. Live Class (Live Teaching)
        Schema::create('lms_live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->string('meeting_url')->nullable();
            $table->enum('status', ['scheduled', 'live', 'ended'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_live_classes');
        Schema::dropIfExists('lms_gamifications');
        Schema::dropIfExists('lms_topic_progress');
        Schema::dropIfExists('lms_topic_quizzes');
        Schema::dropIfExists('lms_topics');
        Schema::dropIfExists('lms_chapters');
    }
};
