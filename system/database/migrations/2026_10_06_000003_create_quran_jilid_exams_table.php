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
        if (!Schema::hasTable('quran_jilid_exams')) {
            Schema::create('quran_jilid_exams', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->index();
                $table->unsignedBigInteger('teacher_id')->nullable()->index(); // Asatidz Penguji
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->date('exam_date');
                $table->string('current_jilid', 50)->default('Jilid 1'); // Jilid yang diuji
                $table->string('target_jilid', 50)->default('Jilid 2'); // Target kenaikan (Jilid 2, 3, 4, Al-Qur'an)
                $table->string('page_tested', 100)->nullable(); // Misal: Hal 1 - 40 / Evaluasi Akhir
                $table->decimal('score_makhraj', 5, 2)->default(0); // Makhorijul & sifat huruf
                $table->decimal('score_mad', 5, 2)->default(0); // Ketepatan mad & panjang pendek
                $table->decimal('score_kelancaran', 5, 2)->default(0); // Kelancaran & fashohah
                $table->decimal('score_final', 5, 2)->default(0);
                $table->string('predicate', 50)->default('Mumtaz');
                $table->string('status', 30)->default('lulus'); // lulus, perbaikan
                $table->string('certificate_number', 100)->nullable()->unique();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['student_id', 'exam_date']);
                $table->index(['current_jilid', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_jilid_exams');
    }
};
