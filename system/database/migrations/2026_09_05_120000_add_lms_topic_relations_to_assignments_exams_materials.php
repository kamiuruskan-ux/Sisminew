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
        // 1. Add LMS Chapter & Topic relations to assignments
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('assignments', 'lms_chapter_id')) {
                    $table->foreignId('lms_chapter_id')->nullable()->after('class_id')->constrained('lms_chapters')->nullOnDelete();
                }
                if (!Schema::hasColumn('assignments', 'lms_topic_id')) {
                    $table->foreignId('lms_topic_id')->nullable()->after('lms_chapter_id')->constrained('lms_topics')->nullOnDelete();
                }
            });
        }

        // 2. Add LMS Chapter & Topic relations to exams
        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                if (!Schema::hasColumn('exams', 'lms_chapter_id')) {
                    $table->foreignId('lms_chapter_id')->nullable()->after('class_id')->constrained('lms_chapters')->nullOnDelete();
                }
                if (!Schema::hasColumn('exams', 'lms_topic_id')) {
                    $table->foreignId('lms_topic_id')->nullable()->after('lms_chapter_id')->constrained('lms_topics')->nullOnDelete();
                }
                if (!Schema::hasColumn('exams', 'exam_type')) {
                    $table->string('exam_type')->default('exam')->after('is_published'); // quiz, practice, exam
                }
            });
        }

        // 3. Add LMS Chapter & Topic relations to materials
        if (Schema::hasTable('materials')) {
            Schema::table('materials', function (Blueprint $table) {
                if (!Schema::hasColumn('materials', 'lms_chapter_id')) {
                    $table->foreignId('lms_chapter_id')->nullable()->after('class_id')->constrained('lms_chapters')->nullOnDelete();
                }
                if (!Schema::hasColumn('materials', 'lms_topic_id')) {
                    $table->foreignId('lms_topic_id')->nullable()->after('lms_chapter_id')->constrained('lms_topics')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropForeign(['lms_chapter_id']);
                $table->dropForeign(['lms_topic_id']);
                $table->dropColumn(['lms_chapter_id', 'lms_topic_id']);
            });
        }

        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropForeign(['lms_chapter_id']);
                $table->dropForeign(['lms_topic_id']);
                $table->dropColumn(['lms_chapter_id', 'lms_topic_id', 'exam_type']);
            });
        }

        if (Schema::hasTable('materials')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropForeign(['lms_chapter_id']);
                $table->dropForeign(['lms_topic_id']);
                $table->dropColumn(['lms_chapter_id', 'lms_topic_id']);
            });
        }
    }
};
