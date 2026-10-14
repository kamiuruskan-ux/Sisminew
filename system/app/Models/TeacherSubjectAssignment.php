<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TeacherSubjectAssignment extends Model
{
    use HasFactory;

    protected $table = 'teacher_subject_assignments';

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject',
        'academic_year_id',
        'notes',
    ];

    /**
     * Memastikan tabel ada secara otomatis (self-migrating pattern)
     */
    public static function ensureTableExists()
    {
        if (!Schema::hasTable('teacher_subject_assignments')) {
            Schema::create('teacher_subject_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teacher_id');
                $table->unsignedBigInteger('class_id');
                $table->string('subject');
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['teacher_id', 'academic_year_id']);
                $table->index(['class_id', 'subject']);
            });
        }
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
