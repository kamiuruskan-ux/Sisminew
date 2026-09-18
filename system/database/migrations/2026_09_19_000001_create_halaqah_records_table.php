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
        if (!Schema::hasTable('halaqah_records')) {
            Schema::create('halaqah_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
                
                $table->date('assessment_date');
                $table->enum('attendance_status', ['hadir', 'sakit', 'izin', 'alpa'])->default('hadir');
                $table->enum('program_type', ['tahsin', 'tahfidz'])->default('tahsin');
                
                // Tahsin details
                $table->string('tahsin_type', 50)->nullable()->default('jilid'); // 'jilid', 'tilawah'
                $table->string('jilid_level', 50)->nullable(); // 'Jilid 1', 'Jilid 2', 'Jilid 3', 'Jilid 4', 'Tilawah'
                $table->integer('page_start')->nullable();
                $table->integer('page_end')->nullable();
                
                // Tahfidz details
                $table->string('surah_name', 100)->nullable(); // e.g. An-Naba, Al-Mulk
                $table->integer('ayat_start')->nullable();
                $table->integer('ayat_end')->nullable();
                $table->integer('juz_number')->nullable()->default(30);
                
                // Scoring (0 - 100)
                $table->decimal('score_cognitive', 5, 2)->default(0); // Nilai kelancaran / materi / makhraj
                $table->decimal('score_adab', 5, 2)->default(0); // Nilai adab / khidmat
                $table->string('predicate', 50)->default('Mumtaz'); // Mumtaz, Jayyid Jiddan, Jayyid, Maqbul
                
                $table->text('teacher_notes')->nullable();
                $table->timestamps();
                
                // Index for speed
                $table->index(['assessment_date', 'program_type']);
                $table->index(['student_id', 'assessment_date']);
                $table->index(['class_id', 'assessment_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('halaqah_records');
    }
};
