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
        // 1. Exams Table
        if (!Schema::hasTable('exams')) {
            Schema::create('exams', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('subject_name');
                $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
                $table->integer('duration_minutes')->default(60);
                $table->timestamp('start_time')->nullable();
                $table->timestamp('end_time')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        // 2. Questions Table
        if (!Schema::hasTable('questions')) {
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
                $table->text('question_text');
                $table->text('option_a');
                $table->text('option_b');
                $table->text('option_c')->nullable();
                $table->text('option_d')->nullable();
                $table->text('option_e')->nullable();
                $table->string('correct_answer', 5); // a, b, c, d, e
                $table->integer('score_weight')->default(10);
                $table->timestamps();
            });
        }

        // 3. Exam Results Table
        if (!Schema::hasTable('exam_results')) {
            Schema::create('exam_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->decimal('score', 5, 2)->default(0);
                $table->json('answers')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->string('status')->default('completed'); // in_progress, completed
                $table->timestamps();
            });
        }

        // 4. Books Table (E-Library)
        if (!Schema::hasTable('books')) {
            Schema::create('books', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('author')->nullable();
                $table->string('publisher')->nullable();
                $table->string('category')->default('Umum');
                $table->string('isbn')->nullable();
                $table->text('description')->nullable();
                $table->string('cover_path')->nullable();
                $table->string('file_path')->nullable(); // E-Book PDF file
                $table->integer('stock')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('books');
    }
};
