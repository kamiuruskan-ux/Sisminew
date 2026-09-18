<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $fillable = [
        'title',
        'description',
        'subject',
        'class_id',
        'lms_chapter_id',
        'lms_topic_id',
        'teacher_id',
        'file_path',
        'file_type',
        'external_link',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(LmsChapter::class, 'lms_chapter_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(LmsTopic::class, 'lms_topic_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForSubject($query, $subject)
    {
        return $query->where('subject', $subject);
    }

    public function isFile(): bool
    {
        return $this->file_path !== null;
    }

    public function isExternalLink(): bool
    {
        return $this->external_link !== null;
    }
}
