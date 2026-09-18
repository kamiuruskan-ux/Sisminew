<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HalaqahRecord extends Model
{
    use HasFactory;

    protected $table = 'halaqah_records';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'class_id',
        'academic_year_id',
        'assessment_date',
        'attendance_status',
        'program_type',
        'tahsin_type',
        'jilid_level',
        'page_start',
        'page_end',
        'surah_name',
        'ayat_start',
        'ayat_end',
        'juz_number',
        'score_cognitive',
        'score_adab',
        'predicate',
        'teacher_notes',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'page_start' => 'integer',
        'page_end' => 'integer',
        'ayat_start' => 'integer',
        'ayat_end' => 'integer',
        'juz_number' => 'integer',
        'score_cognitive' => 'float',
        'score_adab' => 'float',
    ];

    /**
     * Relasi ke Student (Santri)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relasi ke Guru / Musyrif Halaqah (User)
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Relasi ke Kelas
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Relasi ke Tahun Ajaran
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * Helper predikat otomatis dari nilai
     */
    public static function calculatePredicate(float $score): string
    {
        if ($score >= 90) {
            return 'Mumtaz';
        } elseif ($score >= 80) {
            return 'Jayyid Jiddan';
        } elseif ($score >= 70) {
            return 'Jayyid';
        } else {
            return 'Maqbul';
        }
    }

    /**
     * Helper ringkasan materi untuk display list
     */
    public function getMaterialSummaryAttribute(): string
    {
        if ($this->program_type === 'tahsin') {
            $type = $this->tahsin_type === 'tilawah' ? 'Tilawah' : ($this->jilid_level ?? 'Jilid');
            $pages = ($this->page_start && $this->page_end) ? "hl. {$this->page_start} - {$this->page_end}" : ($this->page_start ? "hl. {$this->page_start}" : '');
            return "Tahsin: {$type} {$pages}";
        } else {
            $surah = $this->surah_name ?? 'Surah';
            $ayats = ($this->ayat_start && $this->ayat_end) ? "({$this->ayat_start}-{$this->ayat_end})" : '';
            $juz = $this->juz_number ? " [Juz {$this->juz_number}]" : '';
            return "Tahfidz: Surah {$surah} {$ayats}{$juz}";
        }
    }
}
