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
        // 1. Tambah TMT dan Pendidikan Terakhir ke tabel users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'tmt')) {
                    $table->date('tmt')->nullable()->after('nip')->comment('Terhitung Mulai Tanggal bekerja');
                }
                if (!Schema::hasColumn('users', 'last_education')) {
                    $table->string('last_education', 100)->nullable()->after('tmt')->comment('Pendidikan Terakhir Pegawai');
                }
            });
        }

        // 2. Buat tabel pivot penugasan kelas untuk Guru Al-Qur'an
        if (!Schema::hasTable('quran_teacher_classes')) {
            Schema::create('quran_teacher_classes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('class_id');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
                $table->unique(['user_id', 'class_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quran_teacher_classes')) {
            Schema::dropIfExists('quran_teacher_classes');
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'last_education')) {
                    $table->dropColumn('last_education');
                }
                if (Schema::hasColumn('users', 'tmt')) {
                    $table->dropColumn('tmt');
                }
            });
        }
    }
};
