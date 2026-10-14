<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BpiReport extends Model
{
    use HasFactory;

    protected $table = 'bpi_reports';

    protected $fillable = [
        'student_id',
        'bpi_group_id',
        'academic_year_id',
        'semester',
        'sholat_fardhu',
        'sholat_sunnah',
        'tilawah_quran',
        'dzikir_doa',
        'puasa_sunnah',
        'adab_guru_ortu',
        'kedisiplinan_kebersihan',
        'ukhuwah_sosial',
        'keaktifan_halaqah',
        'total_score',
        'final_predicate',
        'mentor_notes',
        'target_improvement',
        'mentor_id',
    ];

    protected $casts = [
        'total_score' => 'float',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(BpiGroup::class, 'bpi_group_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public static function getPredicateLabel(string $predicate): string
    {
        return match (strtoupper($predicate)) {
            'A' => 'Mumtaz (Sangat Baik)',
            'B' => 'Jayyid Jiddan (Baik Sekali)',
            'C' => 'Jayyid (Baik)',
            'D' => 'Maqbul (Cukup)',
            default => $predicate,
        };
    }
}
