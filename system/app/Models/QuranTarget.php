<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranTarget extends Model
{
    use HasFactory;

    protected $table = 'quran_targets';

    protected $fillable = [
        'grade',
        'semester',
        'program_type',
        'title',
        'target_juz',
        'target_surah_start',
        'target_surah_end',
        'target_jilid',
        'target_page_start',
        'target_page_end',
        'notes',
    ];

    protected $casts = [
        'grade' => 'integer',
        'semester' => 'integer',
        'target_juz' => 'integer',
        'target_page_start' => 'integer',
        'target_page_end' => 'integer',
    ];
}
