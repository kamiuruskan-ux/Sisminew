<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('role')->default('Orang Tua Siswa'); // e.g. 'Orang Tua Siswa', 'Alumni', 'Siswa', 'Komite'
                $table->string('title')->nullable(); // e.g. 'Orang Tua Siswa Kelas 3', 'Alumni'
                $table->text('content');
                $table->unsignedTinyInteger('rating')->default(5); // 1-5
                $table->string('avatar')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'order']);
            });

            // Seed initial testimonials
            DB::table('testimonials')->insert([
                [
                    'name' => 'Ibu Ratna Sari, M.Pd',
                    'role' => 'Orang Tua Siswa',
                    'title' => 'Orang Tua Siswa',
                    'content' => 'SDIT AL-FAHMI PALU memberikan lingkungan belajar yang sangat positif. Para guru sangat perhatian terhadap perkembangan akademik dan kepribadian anak kami.',
                    'rating' => 5,
                    'avatar' => null,
                    'order' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Dimas Prasetyo',
                    'role' => 'Alumni',
                    'title' => 'Alumni',
                    'content' => 'Fasilitas belajar dan bimbingan para ustadz/ustadzah yang luar biasa sangat mendukung pembentukan karakter dan prestasi saya.',
                    'rating' => 5,
                    'avatar' => null,
                    'order' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'dr. Hendra Wijaya',
                    'role' => 'Orang Tua Siswa',
                    'title' => 'Orang Tua Siswa',
                    'content' => 'Lingkungan belajar yang aman, islami, dan nyaman. Pembiasaan ibadah hariannya sangat terasa pengaruh positifnya di rumah.',
                    'rating' => 5,
                    'avatar' => null,
                    'order' => 3,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Muhammad Rizky Pratama',
                    'role' => 'Alumni',
                    'title' => 'Alumni',
                    'content' => 'Pendidikan dasar di SDIT memberikan fondasi agama dan adab yang sangat kuat yang terus membimbing saya di jenjang pendidikan selanjutnya.',
                    'rating' => 5,
                    'avatar' => null,
                    'order' => 4,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Hj. Nurul Hidayati',
                    'role' => 'Orang Tua Siswa',
                    'title' => 'Orang Tua Siswa',
                    'content' => 'Pendidikan karakter dan pembinaan akhlak di sekolah ini berjalan beriringan dengan keunggulan akademik. Sangat kami rekomendasikan untuk para orang tua.',
                    'rating' => 5,
                    'avatar' => null,
                    'order' => 5,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Ahmad Fauzi',
                    'role' => 'Orang Tua Siswa',
                    'title' => 'Orang Tua Siswa',
                    'content' => 'Program tahfidz Al-Qur\'an dan bimbingan guru yang sabar membuat ananda kami sangat bersemangat belajar setiap hari.',
                    'rating' => 5,
                    'avatar' => null,
                    'order' => 6,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
