<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpmbFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'field_key',
        'section',
        'type',
        'options',
        'placeholder',
        'help_text',
        'is_required',
        'is_active',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    public function scopeBySection($query, string $section)
    {
        return $query->where('section', $section);
    }

    public function getSectionLabelAttribute(): string
    {
        return match($this->section) {
            'student' => 'Data Pribadi Calon Siswa',
            'parent' => 'Data Orang Tua / Wali',
            'religious' => 'Keagamaan & Al-Qur\'an',
            'health' => 'Kesehatan & Karakteristik',
            default => 'Kuesioner / Informasi Tambahan',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'text' => 'Teks Singkat',
            'textarea' => 'Teks Panjang / Paragraf',
            'select' => 'Pilihan Dropdown',
            'radio' => 'Pilihan Tunggal (Radio)',
            'checkbox' => 'Pilihan Centang (Checkbox)',
            'number' => 'Angka / Nomor',
            'date' => 'Tanggal',
            default => 'Teks',
        };
    }
}
