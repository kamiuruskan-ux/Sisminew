<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class BpiGroup extends Model
{
    use HasFactory;

    protected $table = 'bpi_groups';

    protected $fillable = [
        'name',
        'mentor_id',
        'academic_year_id',
        'level',
        'gender', // ikhwan, akhwat, all
        'target_juz',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::ensureTableExists();
    }

    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('bpi_groups')) {
                Schema::create('bpi_groups', function ($table) {
                    $table->id();
                    $table->string('name', 150);
                    $table->unsignedBigInteger('mentor_id'); // Pembina / Murabbi
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->string('level', 50)->nullable(); // e.g. Kelas 7, Kelas 8, Tingkat 1
                    $table->string('gender', 20)->default('all'); // ikhwan, akhwat, all
                    $table->string('target_juz', 100)->nullable();
                    $table->text('description')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();

                    $table->index(['mentor_id', 'is_active']);
                });
            }

            if (!Schema::hasTable('bpi_group_members')) {
                Schema::create('bpi_group_members', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('bpi_group_id');
                    $table->unsignedBigInteger('student_id');
                    $table->timestamps();

                    $table->foreign('bpi_group_id', 'fk_bpi_grp_member')
                          ->references('id')->on('bpi_groups')->onDelete('cascade');
                    $table->unique(['bpi_group_id', 'student_id'], 'uniq_bpi_grp_std');
                });
            }

            if (!Schema::hasTable('bpi_agendas')) {
                Schema::create('bpi_agendas', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('bpi_group_id');
                    $table->unsignedBigInteger('mentor_id');
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->date('date');
                    $table->integer('meeting_number')->default(1);
                    $table->string('topic', 200); // Materi Tarbiyah / Tema Pekanan
                    $table->text('material_summary')->nullable(); // Ringkasan Materi
                    $table->string('tilawah_surah', 150)->nullable(); // Surah / Ayat yang dibaca bersama
                    $table->text('mutabaah_notes')->nullable(); // Catatan Evaluasi Ibadah Yaumiyah
                    $table->text('general_notes')->nullable();
                    $table->timestamps();

                    $table->index(['bpi_group_id', 'date']);
                    $table->index(['mentor_id', 'date']);
                });
            }

            if (!Schema::hasTable('bpi_attendances')) {
                Schema::create('bpi_attendances', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('bpi_agenda_id');
                    $table->unsignedBigInteger('student_id');
                    $table->string('attendance_status', 20)->default('hadir'); // hadir, sakit, izin, alpa
                    $table->integer('tilawah_pages')->default(0); // Lembar tilawah sepekan
                    $table->decimal('score_attitude', 5, 2)->default(85); // Nilai Adab & Keaktifan
                    $table->text('mutabaah_status')->nullable(); // Sholat 5 Waktu, Dhuha, dll
                    $table->text('notes')->nullable();
                    $table->timestamps();

                    $table->foreign('bpi_agenda_id', 'fk_bpi_att_agenda')
                          ->references('id')->on('bpi_agendas')->onDelete('cascade');
                    $table->index(['student_id', 'bpi_agenda_id'], 'idx_bpi_std_agenda');
                });
            }

            if (!Schema::hasTable('bpi_reports')) {
                Schema::create('bpi_reports', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('bpi_group_id');
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->string('semester', 20)->default('ganjil'); // ganjil, genap
                    
                    // Aspek Ibadah Yaumiyah
                    $table->string('sholat_fardhu', 10)->default('A'); // A: Sangat Baik / Tertib Berjamaah, B: Baik, C: Cukup
                    $table->string('sholat_sunnah', 10)->default('A'); // Dhuha & Tahajjud/Rawatib
                    $table->string('tilawah_quran', 10)->default('A'); // Target Khatam & Tilawah Harian
                    $table->string('dzikir_doa', 10)->default('A'); // Al-Matsurat / Doa Sehari-hari
                    $table->string('puasa_sunnah', 10)->default('B'); // Senin-Kamis / Ayyamul Bidh
                    
                    // Aspek Adab, Akhlak & Kepribadian
                    $table->string('adab_guru_ortu', 10)->default('A'); // Birrul Walidain & Hormat Guru
                    $table->string('kedisiplinan_kebersihan', 10)->default('A');
                    $table->string('ukhuwah_sosial', 10)->default('A'); // Kepedulian sesama teman
                    $table->string('keaktifan_halaqah', 10)->default('A'); // Interaksi dan kehadiran mentoring

                    // Nilai Rata-rata & Predikat Akhir
                    $table->decimal('total_score', 5, 2)->default(88);
                    $table->string('final_predicate', 10)->default('A'); // Mumtaz (A), Jayyid Jiddan (B), Jayyid (C), Maqbul (D)
                    $table->text('mentor_notes')->nullable(); // Catatan Pembina / Murabbi
                    $table->text('target_improvement')->nullable(); // Target Peningkatan Semester Depan
                    $table->unsignedBigInteger('mentor_id')->nullable();
                    $table->timestamps();

                    $table->unique(['student_id', 'bpi_group_id', 'academic_year_id', 'semester'], 'uniq_bpi_report_std');
                });
            }
        } catch (\Throwable $e) {
            // Silently handle
        }
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(BpiGroupMember::class, 'bpi_group_id');
    }

    public function agendas(): HasMany
    {
        return $this->hasMany(BpiAgenda::class, 'bpi_group_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(BpiReport::class, 'bpi_group_id');
    }
}
