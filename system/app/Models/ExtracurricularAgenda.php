<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class ExtracurricularAgenda extends Model
{
    use HasFactory;

    protected $table = 'extracurricular_agendas';

    protected $fillable = [
        'extracurricular_id',
        'coach_id',
        'academic_year_id',
        'date',
        'meeting_number',
        'activity_title',
        'material_taught',
        'location',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'meeting_number' => 'integer',
    ];

    protected static function booted()
    {
        static::ensureTableExists();
    }

    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('extracurricular_agendas')) {
                Schema::create('extracurricular_agendas', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('extracurricular_id');
                    $table->unsignedBigInteger('coach_id');
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->date('date');
                    $table->integer('meeting_number')->default(1);
                    $table->string('activity_title', 200)->nullable();
                    $table->text('material_taught');
                    $table->string('location', 150)->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();

                    $table->index(['extracurricular_id', 'date']);
                    $table->index(['coach_id', 'date']);
                });
            }

            if (!Schema::hasTable('extracurricular_agenda_students')) {
                Schema::create('extracurricular_agenda_students', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('extracurricular_agenda_id');
                    $table->unsignedBigInteger('student_id');
                    $table->string('attendance_status', 20)->default('hadir'); // hadir, sakit, izin, alpa
                    $table->decimal('score_skill', 5, 2)->default(80);
                    $table->decimal('score_attitude', 5, 2)->default(80);
                    $table->text('notes')->nullable();
                    $table->timestamps();

                    $table->foreign('extracurricular_agenda_id', 'fk_extra_agenda_std')
                          ->references('id')->on('extracurricular_agendas')->onDelete('cascade');
                    $table->index(['student_id', 'extracurricular_agenda_id'], 'idx_extra_std_agenda');
                });
            }

            if (!Schema::hasTable('extracurricular_evaluations')) {
                Schema::create('extracurricular_evaluations', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('extracurricular_id');
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->string('semester', 20)->default('ganjil'); // ganjil, genap
                    $table->string('predicate', 20)->default('A'); // A, B, C, D
                    $table->decimal('score', 5, 2)->default(85);
                    $table->text('description')->nullable(); // Deskripsi capaian untuk rapor
                    $table->unsignedBigInteger('evaluated_by')->nullable();
                    $table->timestamps();

                    $table->unique(['extracurricular_id', 'student_id', 'academic_year_id', 'semester'], 'uniq_extra_eval_std');
                });
            }
        } catch (\Throwable $e) {
            // Silently handle if table exists
        }
    }

    public function extracurricular(): BelongsTo
    {
        return $this->belongsTo(Extracurricular::class, 'extracurricular_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(ExtracurricularAgendaStudent::class, 'extracurricular_agenda_id');
    }

    public function getAttendanceCountAttribute(): array
    {
        $counts = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total' => 0,
        ];

        foreach ($this->students as $st) {
            $status = strtolower($st->attendance_status);
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
            $counts['total']++;
        }

        return $counts;
    }
}
