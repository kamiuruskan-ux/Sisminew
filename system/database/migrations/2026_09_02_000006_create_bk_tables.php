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
        // 1. Tabel Sesi Layanan Konseling BK (Pribadi, Sosial, Belajar, Karier, Kedisiplinan)
        Schema::create('bk_counselings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete(); // Guru BK
            $table->enum('category', ['pribadi', 'sosial', 'belajar', 'karier', 'kedisiplinan'])->default('pribadi');
            $table->enum('service_type', ['individu', 'kelompok', 'klasikal'])->default('individu');
            $table->string('title'); // Topik / Permasalahan
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('place')->nullable(); // Ruang BK / Kelas / Online
            $table->text('complaint_notes')->nullable(); // Gejala / Latar Belakang / Keluhan
            $table->text('action_plan')->nullable(); // Rencana Intervensi / Solusi
            $table->text('follow_up_notes')->nullable(); // Evaluasi & Tindak Lanjut
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'referred'])->default('completed'); // Referred: Diberikan penanganan ahli luar
            $table->boolean('is_confidential')->default(false); // Kerahasiaan Sesi
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Asesmen & Bimbingan Karir / Minat Bakat Siswa
        Schema::create('bk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title'); // Judul Asesmen / Pemetaan Minat Bakat
            $table->enum('type', ['angket_minat', 'bakat_karier', 'sosiometri', 'psikotes', 'observasi_perilaku'])->default('angket_minat');
            $table->string('dream_career')->nullable(); // Cita-cita / Karir Impian
            $table->string('recommended_major')->nullable(); // Rekomendasi Jurusan / Perguruan Tinggi
            $table->text('strength_notes')->nullable(); // Kelebihan & Potensi
            $table->text('improvement_notes')->nullable(); // Area Pengembangan
            $table->string('attachment')->nullable(); // File hasil tes/angket
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_assessments');
        Schema::dropIfExists('bk_counselings');
    }
};
