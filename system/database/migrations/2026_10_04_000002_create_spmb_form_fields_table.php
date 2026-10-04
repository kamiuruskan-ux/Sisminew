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
        // 1. Add custom_fields JSON column to spmb_registrations if not exists
        if (Schema::hasTable('spmb_registrations') && !Schema::hasColumn('spmb_registrations', 'custom_fields')) {
            Schema::table('spmb_registrations', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('origin_school');
            });
        }

        // 2. Create spmb_form_fields table
        if (!Schema::hasTable('spmb_form_fields')) {
            Schema::create('spmb_form_fields', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->string('field_key')->unique();
                $table->string('section')->default('student'); // 'student', 'parent', 'religious', 'health', 'other'
                $table->string('type')->default('text'); // 'text', 'textarea', 'number', 'select', 'radio', 'checkbox', 'date'
                $table->json('options')->nullable(); // For select, radio, checkbox
                $table->string('placeholder')->nullable();
                $table->string('help_text')->nullable();
                $table->boolean('is_required')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();

                $table->index(['section', 'is_active', 'order']);
            });

            // Seed initial relevant questions for SDIT
            $initialFields = [
                // Section: Data Orang Tua
                [
                    'label' => 'Nama Lengkap Ayah Kandung',
                    'field_key' => 'nama_ayah',
                    'section' => 'parent',
                    'type' => 'text',
                    'options' => null,
                    'placeholder' => 'Nama lengkap ayah beserta gelar',
                    'help_text' => 'Tuliskan nama lengkap ayah calon siswa',
                    'is_required' => true,
                    'is_active' => true,
                    'order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'label' => 'Nama Lengkap Ibu Kandung',
                    'field_key' => 'nama_ibu',
                    'section' => 'parent',
                    'type' => 'text',
                    'options' => null,
                    'placeholder' => 'Nama lengkap ibu beserta gelar',
                    'help_text' => 'Tuliskan nama lengkap ibu calon siswa',
                    'is_required' => true,
                    'is_active' => true,
                    'order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'label' => 'Pekerjaan Orang Tua / Wali',
                    'field_key' => 'pekerjaan_ortu',
                    'section' => 'parent',
                    'type' => 'select',
                    'options' => json_encode(['PNS / ASN', 'TNI / POLRI', 'Karyawan Swasta', 'Wiraswasta / Pengusaha', 'Dokter / Tenaga Medis', 'Guru / Dosen', 'Petani / Nelayan', 'Ibu Rumah Tangga', 'Lainnya']),
                    'placeholder' => 'Pilih pekerjaan orang tua',
                    'help_text' => 'Pilih pekerjaan utama orang tua/wali',
                    'is_required' => false,
                    'is_active' => true,
                    'order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'label' => 'Penghasilan Rata-rata Orang Tua / Bulan',
                    'field_key' => 'penghasilan_ortu',
                    'section' => 'parent',
                    'type' => 'select',
                    'options' => json_encode(['< Rp 2.000.000', 'Rp 2.000.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000']),
                    'placeholder' => 'Pilih rentang penghasilan',
                    'help_text' => 'Informasi ini digunakan untuk pendataan dan pemetaan bantuan jika ada',
                    'is_required' => false,
                    'is_active' => true,
                    'order' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // Section: Keagamaan & Karakter
                [
                    'label' => 'Kemampuan Membaca Al-Qur\'an Ananda Saat Ini',
                    'field_key' => 'kemampuan_mengaji',
                    'section' => 'religious',
                    'type' => 'select',
                    'options' => json_encode(['Belum Mengenal Huruf Hijaiyah', 'Sedang Belajar Iqro / Tilawati (Jilid 1-3)', 'Sedang Belajar Iqro / Tilawati (Jilid 4-6)', 'Sudah Mulai Membaca Al-Qur\'an', 'Sudah Lancar Membaca Al-Qur\'an']),
                    'placeholder' => 'Pilih tingkat kemampuan',
                    'help_text' => 'Digunakan untuk pemetaan halaqah Al-Qur\'an saat mulai masuk sekolah',
                    'is_required' => true,
                    'is_active' => true,
                    'order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'label' => 'Jumlah Hafalan Surah / Doa Harian yang Sudah Dikuasai',
                    'field_key' => 'hafalan_awal',
                    'section' => 'religious',
                    'type' => 'text',
                    'options' => null,
                    'placeholder' => 'Contoh: Surah An-Nas s/d Al-Fil, Doa Makan, Doa Tidur',
                    'help_text' => 'Tuliskan surah pendek atau doa yang sudah hafal (opsional)',
                    'is_required' => false,
                    'is_active' => true,
                    'order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // Section: Kesehatan & Karakteristik
                [
                    'label' => 'Golongan Darah Anak',
                    'field_key' => 'golongan_darah',
                    'section' => 'health',
                    'type' => 'select',
                    'options' => json_encode(['A', 'B', 'AB', 'O', 'Belum Tahu']),
                    'placeholder' => 'Pilih golongan darah',
                    'help_text' => 'Pilih golongan darah calon siswa',
                    'is_required' => false,
                    'is_active' => true,
                    'order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'label' => 'Anak Ke- berapa dari berapa bersaudara?',
                    'field_key' => 'anak_ke',
                    'section' => 'student',
                    'type' => 'text',
                    'options' => null,
                    'placeholder' => 'Contoh: Anak ke 2 dari 3 bersaudara',
                    'help_text' => 'Keterangan urutan anak dalam keluarga',
                    'is_required' => false,
                    'is_active' => true,
                    'order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'label' => 'Riwayat Alergi / Penyakit Khusus yang Perlu Diperhatikan',
                    'field_key' => 'riwayat_kesehatan',
                    'section' => 'health',
                    'type' => 'textarea',
                    'options' => null,
                    'placeholder' => 'Tuliskan jika anak memiliki riwayat asma, alergi makanan tertentu, dll. (Tulis "-" jika tidak ada)',
                    'help_text' => 'Sangat penting untuk penanganan medis darurat oleh pihak UKS sekolah',
                    'is_required' => false,
                    'is_active' => true,
                    'order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // Section: Kuesioner Tambahan
                [
                    'label' => 'Mengetahui Informasi Pendaftaran SDIT Al-Fahmi Dari Mana?',
                    'field_key' => 'sumber_informasi',
                    'section' => 'other',
                    'type' => 'select',
                    'options' => json_encode(['Media Sosial (Instagram / Facebook)', 'Spanduk / Brosur Sekolah', 'Rekomendasi Keluarga / Teman', 'Alumni / Orang Tua Siswa Lain', 'Website Resmi Sekolah', 'Lainnya']),
                    'placeholder' => 'Pilih sumber informasi',
                    'help_text' => 'Bantu kami mengetahui saluran informasi yang paling efektif',
                    'is_required' => false,
                    'is_active' => true,
                    'order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('spmb_form_fields')->insert($initialFields);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_form_fields');

        if (Schema::hasTable('spmb_registrations') && Schema::hasColumn('spmb_registrations', 'custom_fields')) {
            Schema::table('spmb_registrations', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }
    }
};
