<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkStudentViolation extends Model
{
    use HasFactory;

    protected $table = 'bk_student_violations';

    protected $fillable = [
        'student_id',
        'violation_category_id',
        'counselor_id',
        'violation_date',
        'title',
        'points',
        'notes',
        'penalty',
        'status',
        'attachment',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'points' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function category()
    {
        return $this->belongsTo(BkViolationCategory::class, 'violation_category_id');
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($this->attachment, ['http://', 'https://', 'doc/', 'img/'])) {
            return asset($this->attachment);
        }

        return asset('doc/' . $this->attachment);
    }
}
