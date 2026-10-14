<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ActivityAttendance extends Model
{
    use HasFactory;

    protected $table = 'activity_attendances';

    protected $fillable = [
        'event_id',
        'participant_type', // 'student' | 'teacher'
        'student_id',
        'user_id',
        'status', // 'present', 'permission', 'sick', 'absent'
        'check_in_time',
        'notes',
        'recorded_by',
    ];

    protected static function boot()
    {
        parent::boot();
        static::ensureTableExists();
    }

    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('activity_attendances')) {
                Schema::create('activity_attendances', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('event_id');
                    $table->string('participant_type', 20)->default('student');
                    $table->unsignedBigInteger('student_id')->nullable();
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->string('status', 20)->default('present');
                    $table->string('check_in_time', 10)->nullable();
                    $table->string('notes')->nullable();
                    $table->unsignedBigInteger('recorded_by')->nullable();
                    $table->timestamps();

                    $table->index(['event_id', 'student_id']);
                    $table->index(['event_id', 'user_id']);
                });
            }
        } catch (\Throwable $e) {}
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(ActivityEvent::class, 'event_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
