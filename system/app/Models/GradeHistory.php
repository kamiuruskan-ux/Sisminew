<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class GradeHistory extends Model
{
    use HasFactory;

    protected $table = 'grade_histories';

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'semester',
        'subject',
        'type', // 'daily', 'pts', 'pas'
        'old_score',
        'new_score',
        'source', // 'auto', 'manual'
        'changed_by',
        'notes',
    ];

    /**
     * Memastikan tabel ada secara otomatis (self-migrating pattern)
     */
    public static function ensureTableExists()
    {
        if (!Schema::hasTable('grade_histories')) {
            Schema::create('grade_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->string('semester', 20)->nullable();
                $table->string('subject');
                $table->string('type', 20)->default('daily');
                $table->decimal('old_score', 5, 2)->nullable();
                $table->decimal('new_score', 5, 2)->nullable();
                $table->string('source', 20)->default('manual');
                $table->unsignedBigInteger('changed_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['student_id', 'subject', 'type']);
                $table->index(['class_id', 'academic_year_id']);
            });
        }
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
