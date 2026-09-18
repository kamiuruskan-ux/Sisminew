<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkCounseling extends Model
{
    use HasFactory;

    protected $table = 'bk_counselings';

    protected $fillable = [
        'student_id',
        'counselor_id',
        'category',
        'service_type',
        'title',
        'date',
        'time',
        'place',
        'complaint_notes',
        'action_plan',
        'follow_up_notes',
        'status',
        'is_confidential',
        'attachment',
    ];

    protected $casts = [
        'date' => 'date',
        'is_confidential' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'pribadi' => 'Bimbingan Pribadi',
            'sosial' => 'Bimbingan Sosial',
            'belajar' => 'Bimbingan Belajar',
            'karier' => 'Bimbingan Karier & Masa Depan',
            'kedisiplinan' => 'Kedisiplinan & Perilaku',
            default => ucfirst($this->category),
        };
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return match ($this->service_type) {
            'individu' => 'Konseling Individu',
            'kelompok' => 'Bimbingan Kelompok',
            'klasikal' => 'Bimbingan Klasikal (Kelas)',
            default => ucfirst($this->service_type),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Selesai',
            'in_progress' => 'Dalam Proses Intervensi',
            'scheduled' => 'Terjadwal',
            'referred' => 'Alih Tangan Kasus (Rujukan)',
            default => ucfirst($this->status),
        };
    }
}
