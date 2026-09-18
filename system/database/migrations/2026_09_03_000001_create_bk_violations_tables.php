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
        // 1. Master Kategori & Poin Pelanggaran Siswa
        Schema::create('bk_violation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Pelanggaran (contoh: Keterlambatan, Merokok, Bolos, Perundungan)
            $table->enum('level', ['ringan', 'sedang', 'berat', 'sangat_berat'])->default('ringan');
            $table->integer('points')->default(5); // Poin sanksi
            $table->text('penalty_recommendation')->nullable(); // Rekomendasi sanksi
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Transaksi Catatan Pelanggaran Siswa
        Schema::create('bk_student_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('violation_category_id')->nullable()->constrained('bk_violation_categories')->nullOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete(); // Guru BK / Pelapor
            $table->date('violation_date');
            $table->string('title'); // Nama / Ringkasan Pelanggaran
            $table->integer('points')->default(0); // Poin yang dikenakan
            $table->text('notes')->nullable(); // Kronologi / Catatan Kejadian
            $table->text('penalty')->nullable(); // Sanksi / Tindakan yang diberikan
            $table->enum('status', ['pending', 'processed', 'sp1', 'sp2', 'sp3', 'resolved', 'dismissed'])->default('processed');
            $table->string('attachment')->nullable(); // Foto Bukti / Dokumen
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_student_violations');
        Schema::dropIfExists('bk_violation_categories');
    }
};
