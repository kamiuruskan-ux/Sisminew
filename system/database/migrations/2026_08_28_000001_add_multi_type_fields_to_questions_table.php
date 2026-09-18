<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'type')) {
                $table->string('type')->default('pg')->after('exam_id'); // pg, pg_kompleks, benar_salah, menjodohkan, essay
            }
            if (!Schema::hasColumn('questions', 'image_path')) {
                $table->string('image_path')->nullable()->after('question_text');
            }
            if (!Schema::hasColumn('questions', 'options_json')) {
                $table->json('options_json')->nullable()->after('option_e');
            }
            if (!Schema::hasColumn('questions', 'correct_answer_json')) {
                $table->json('correct_answer_json')->nullable()->after('correct_answer');
            }
        });

        // Modify columns to be nullable/flexible for non-PG question types
        DB::statement("ALTER TABLE questions MODIFY correct_answer TEXT NULL");
        DB::statement("ALTER TABLE questions MODIFY option_a TEXT NULL");
        DB::statement("ALTER TABLE questions MODIFY option_b TEXT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'image_path', 'options_json', 'correct_answer_json']);
        });
    }
};
