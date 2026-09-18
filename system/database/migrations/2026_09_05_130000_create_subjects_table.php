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
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('code')->nullable();
                $table->string('name')->unique();
                $table->string('category')->default('Umum'); // Umum, Kejuruan, Muatan Lokal, Pilihan
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Default initial seed of standard subjects
            $defaultSubjects = [
                ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam (IPA)', 'category' => 'Umum', 'order' => 1],
                ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial (IPS)', 'category' => 'Umum', 'order' => 2],
                ['code' => 'MTK', 'name' => 'Matematika', 'category' => 'Umum', 'order' => 3],
                ['code' => 'BIN', 'name' => 'Bahasa Indonesia', 'category' => 'Umum', 'order' => 4],
                ['code' => 'BIG', 'name' => 'Bahasa Inggris', 'category' => 'Umum', 'order' => 5],
                ['code' => 'FIS', 'name' => 'Fisika', 'category' => 'Umum', 'order' => 6],
                ['code' => 'KIM', 'name' => 'Kimia', 'category' => 'Umum', 'order' => 7],
                ['code' => 'BIO', 'name' => 'Biologi', 'category' => 'Umum', 'order' => 8],
                ['code' => 'PAI', 'name' => 'Pendidikan Agama & Budi Pekerti', 'category' => 'Umum', 'order' => 9],
                ['code' => 'PKN', 'name' => 'Pendidikan Pancasila & Kewarganegaraan (PPKn)', 'category' => 'Umum', 'order' => 10],
                ['code' => 'PJK', 'name' => 'Pendidikan Jasmani, Olahraga & Kesehatan (PJOK)', 'category' => 'Umum', 'order' => 11],
                ['code' => 'INF', 'name' => 'Informatika / Komputer', 'category' => 'Umum', 'order' => 12],
                ['code' => 'SNB', 'name' => 'Seni Budaya & Prakarya', 'category' => 'Umum', 'order' => 13],
                ['code' => 'SEJ', 'name' => 'Sejarah Indonesia', 'category' => 'Umum', 'order' => 14],
            ];

            foreach ($defaultSubjects as $sub) {
                DB::table('subjects')->insertOrIgnore([
                    'code' => $sub['code'],
                    'name' => $sub['name'],
                    'category' => $sub['category'],
                    'order' => $sub['order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Also seed any subjects currently used in lms_chapters, materials, or exams
            $existingLmsSubjects = DB::table('lms_chapters')->distinct()->pluck('subject');
            foreach ($existingLmsSubjects as $extSubject) {
                if (!empty($extSubject)) {
                    DB::table('subjects')->insertOrIgnore([
                        'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $extSubject), 0, 4)),
                        'name' => trim($extSubject),
                        'category' => 'Umum',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
