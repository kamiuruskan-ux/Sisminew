<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuranHalaqahMember extends Model
{
    use HasFactory;

    protected $table = 'quran_halaqah_members';

    protected $fillable = [
        'teacher_id',
        'student_id',
        'academic_year_id',
        'grade',
        'group_name',
    ];

    protected $casts = [
        'grade' => 'integer',
    ];

    /**
     * Guru Pembimbing Halaqah
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Santri Anggota Halaqah
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Tahun Ajaran
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
