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
        // 1. student_class_histories
        if (!Schema::hasTable('student_class_histories')) {
            Schema::create('student_class_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('academic_year_id');
                $table->string('status', 30)->default('active'); // active, promoted, retained, graduated, transferred
                $table->text('notes')->nullable();
                $table->timestamp('promoted_at')->nullable();
                $table->unsignedBigInteger('promoted_by')->nullable();
                $table->timestamps();

                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                $table->index(['student_id', 'academic_year_id'], 'idx_sch_std_year');
                $table->index(['class_id', 'academic_year_id'], 'idx_sch_cls_year');
                $table->index('status', 'idx_sch_status');
            });
        }

        // 2. grades academic_year_id & semester
        if (Schema::hasTable('grades')) {
            Schema::table('grades', function (Blueprint $table) {
                if (!Schema::hasColumn('grades', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('class_id')->index();
                }
                if (!Schema::hasColumn('grades', 'semester')) {
                    $table->string('semester', 20)->default('ganjil')->after('academic_year_id')->index();
                }
            });
        }

        // 3. activity_events & activity_attendances
        if (Schema::hasTable('activity_events')) {
            Schema::table('activity_events', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_events', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('class_id')->index();
                }
            });
        }
        if (Schema::hasTable('activity_attendances')) {
            Schema::table('activity_attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_attendances', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('event_id')->index();
                }
                if (!Schema::hasColumn('activity_attendances', 'class_id')) {
                    $table->unsignedBigInteger('class_id')->nullable()->after('student_id')->index();
                }
            });
        }

        // 4. kpi_evaluations & teacher_attendances
        if (Schema::hasTable('kpi_evaluations')) {
            Schema::table('kpi_evaluations', function (Blueprint $table) {
                if (!Schema::hasColumn('kpi_evaluations', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('period_semester')->index();
                }
            });
        }
        if (Schema::hasTable('teacher_attendances')) {
            Schema::table('teacher_attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('teacher_attendances', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('date')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_class_histories');
    }
};
