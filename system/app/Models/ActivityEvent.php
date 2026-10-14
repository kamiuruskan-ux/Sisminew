<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ActivityEvent extends Model
{
    use HasFactory;

    protected $table = 'activity_events';

    protected $fillable = [
        'title',
        'category',
        'date_start',
        'date_end',
        'time_start',
        'time_end',
        'location',
        'target_audience',
        'class_id',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::ensureTableExists();
    }

    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('activity_events')) {
                Schema::create('activity_events', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('category', 50)->default('general'); // academic, religious, competition, holiday, meeting, extracurricular, general
                    $table->date('date_start');
                    $table->date('date_end')->nullable();
                    $table->string('time_start', 10)->nullable();
                    $table->string('time_end', 10)->nullable();
                    $table->string('location')->nullable();
                    $table->string('target_audience', 50)->default('all'); // all, students, teachers, class
                    $table->unsignedBigInteger('class_id')->nullable();
                    $table->text('description')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->timestamps();

                    $table->index(['date_start', 'category']);
                });
            }
        } catch (\Throwable $e) {}
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class, 'event_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'academic' => 'Akademik & Ujian',
            'religious' => 'Keagamaan & PHBI',
            'competition' => 'Lomba & Prestasi',
            'holiday' => 'Libur Sekolah',
            'meeting' => 'Rapat & Briefing',
            'extracurricular' => 'Ekstrakurikuler',
            default => 'Kegiatan Umum',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'academic' => 'indigo',
            'religious' => 'emerald',
            'competition' => 'amber',
            'holiday' => 'rose',
            'meeting' => 'purple',
            'extracurricular' => 'sky',
            default => 'slate',
        };
    }
}
