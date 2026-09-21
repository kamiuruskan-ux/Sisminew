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
        if (!Schema::hasTable('kpi_evaluations')) {
            Schema::create('kpi_evaluations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); // Teacher / Employee
                $table->unsignedBigInteger('evaluator_id')->nullable(); // Principal / Evaluator
                $table->string('period_type', 20)->default('month'); // 'month', 'semester', 'year'
                $table->unsignedSmallInteger('period_year');
                $table->unsignedTinyInteger('period_month')->nullable(); // 1-12
                $table->unsignedTinyInteger('period_semester')->nullable(); // 1 or 2
                
                // Component Scores (0 - 100)
                $table->decimal('score_comp_1', 5, 2)->default(0); // Disiplin & Presensi
                $table->decimal('score_comp_2', 5, 2)->default(0); // Pedagogik / Pembelajaran
                $table->decimal('score_comp_3', 5, 2)->default(0); // Profesional & Pengembangan Diri
                $table->decimal('score_comp_4', 5, 2)->default(0); // Kepribadian & Nilai Tarbiyah
                $table->decimal('score_comp_5', 5, 2)->default(0); // Sosial & Kolaborasi

                // Final Score & Predicate
                $table->decimal('final_score', 5, 2)->default(0);
                $table->string('predicate', 10)->default('C'); // A, B, C, D
                $table->decimal('attendance_percentage', 5, 2)->default(0);
                $table->boolean('gate_passed')->default(true); // Minimum 85% attendance
                $table->boolean('is_capped')->default(false);

                // Feedback & Recommendations
                $table->text('feedback_appreciation')->nullable();
                $table->text('feedback_improvement')->nullable();
                $table->string('status', 20)->default('draft'); // draft, published
                $table->timestamp('published_at')->nullable();

                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('evaluator_id')->references('id')->on('users')->onDelete('set null');
                $table->unique(['user_id', 'period_year', 'period_month', 'period_type'], 'kpi_user_period_unique');
                $table->index(['period_year', 'period_month']);
                $table->index(['predicate', 'final_score']);
            });
        }

        if (!Schema::hasTable('kpi_evaluation_items')) {
            Schema::create('kpi_evaluation_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('kpi_evaluation_id');
                $table->string('indicator_code', 30); // 1.1, 1.2, 1.3, 1.4, 2.1.1, etc.
                $table->decimal('score', 5, 2)->default(0);
                $table->string('source_type', 20)->default('auto'); // auto, manual, hybrid
                $table->string('source_detail', 255)->nullable(); // e.g. "Presensi 95%, 15 mnt telat"
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('kpi_evaluation_id')->references('id')->on('kpi_evaluations')->onDelete('cascade');
                $table->unique(['kpi_evaluation_id', 'indicator_code'], 'kpi_item_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_evaluation_items');
        Schema::dropIfExists('kpi_evaluations');
    }
};
