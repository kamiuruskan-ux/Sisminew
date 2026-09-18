<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkAssessment extends Model
{
    use HasFactory;

    protected $table = 'bk_assessments';

    protected $fillable = [
        'student_id',
        'counselor_id',
        'title',
        'type',
        'dream_career',
        'recommended_major',
        'strength_notes',
        'improvement_notes',
        'attachment',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'angket_minat' => 'Angket Minat & Potensi',
            'bakat_karier' => 'Pemetaan Bakat & Karir',
            'sosiometri' => 'Sosiometri / Hubungan Sosial',
            'psikotes' => 'Hasil Tes Psikologi / IQ',
            'observasi_perilaku' => 'Observasi Perkembangan',
            default => ucfirst($this->type),
        };
    }
}
