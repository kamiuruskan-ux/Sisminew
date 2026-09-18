<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsChapter extends Model
{
    protected $fillable = [
        'class_id',
        'subject',
        'title',
        'description',
        'cover_image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(LmsTopic::class, 'lms_chapter_id')->orderBy('order', 'asc');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'lms_chapter_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'lms_chapter_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'lms_chapter_id');
    }
}

