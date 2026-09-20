<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class TeachingAgenda extends Model
{
    use HasFactory;

    protected $table = 'teaching_agendas';

    protected $fillable = [
        'teacher_id',
        'class_id',
        'academic_year_id',
        'subject',
        'date',
        'material_taught',
        'class_notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function booted()
    {
        static::ensureTableExists();
    }

    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('teaching_agendas')) {
                Schema::create('teaching_agendas', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('teacher_id');
                    $table->unsignedBigInteger('class_id');
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->string('subject', 150);
                    $table->date('date');
                    $table->text('material_taught');
                    $table->text('class_notes')->nullable();
                    $table->timestamps();

                    $table->index(['teacher_id', 'date']);
                    $table->index(['class_id', 'subject']);
                });
            }

            if (!Schema::hasTable('teaching_agenda_students')) {
                Schema::create('teaching_agenda_students', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('teaching_agenda_id');
                    $table->unsignedBigInteger('student_id');
                    $table->string('attendance_status', 20)->default('hadir');
                    $table->decimal('score_cognitive', 5, 2)->default(80);
                    $table->decimal('score_adab', 5, 2)->default(80);
                    $table->text('notes')->nullable();
                    $table->timestamps();

                    $table->foreign('teaching_agenda_id')->references('id')->on('teaching_agendas')->onDelete('cascade');
                    $table->index(['student_id', 'teaching_agenda_id']);
                });
            }
        } catch (\Throwable $e) {
            // Silently handle if already created or db connection issue
        }
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(TeachingAgendaStudent::class, 'teaching_agenda_id');
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

    public function getAvgCognitiveAttribute(): float
    {
        return (float) ($this->students->avg('score_cognitive') ?? 0);
    }

    public function getAvgAdabAttribute(): float
    {
        return (float) ($this->students->avg('score_adab') ?? 0);
    }
}
