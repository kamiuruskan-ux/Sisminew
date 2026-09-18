<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'subject',
        'class_id',
        'lms_chapter_id',
        'lms_topic_id',
        'teacher_id',
        'due_date',
        'attachment',
        'max_score',
        'status',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'max_score' => 'integer',
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

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays($days));
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', 'published');
    }
}
