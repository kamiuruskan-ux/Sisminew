<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class ExtracurricularMember extends Model
{
    use HasFactory;

    protected $table = 'extracurricular_members';

    protected $fillable = [
        'extracurricular_id',
        'student_id',
        'academic_year_id',
        'notes',
    ];

    protected static function booted()
    {
        static::ensureTableExists();
    }

    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('extracurricular_members')) {
                Schema::create('extracurricular_members', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('extracurricular_id');
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();

                    $table->index(['extracurricular_id', 'academic_year_id']);
                    $table->index('student_id');
                });
            }
        } catch (\Throwable $e) {
            // Table already exists or handled by DB
        }
    }

    public function extracurricular(): BelongsTo
    {
        return $this->belongsTo(Extracurricular::class, 'extracurricular_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
