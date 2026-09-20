<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class EmployeeStudySession extends Model
{
    protected $table = 'employee_study_sessions';

    protected $fillable = [
        'title',
        'speaker',
        'date',
        'time_start',
        'time_end',
        'location',
        'material_summary',
        'attachment_path',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EmployeeStudyAttendance::class, 'session_id');
    }

    public function getHadirCountAttribute(): int
    {
        return $this->attendances()->where('status', 'hadir')->count();
    }

    public function getIzinCountAttribute(): int
    {
        return $this->attendances()->where('status', 'izin')->count();
    }

    public function getSakitCountAttribute(): int
    {
        return $this->attendances()->where('status', 'sakit')->count();
    }

    public function getAlpaCountAttribute(): int
    {
        return $this->attendances()->where('status', 'alpa')->count();
    }

    public function getTotalParticipantsAttribute(): int
    {
        return $this->attendances()->count();
    }

    public function getPercentageHadirAttribute(): float
    {
        $total = $this->total_participants;
        if ($total === 0) return 0;
        return round(($this->hadir_count / $total) * 100, 1);
    }

    protected static function booted()
    {
        static::ensureTablesExist();
    }

    public static function ensureTablesExist(): void
    {
        try {
            if (!Schema::hasTable('employee_study_sessions')) {
                Schema::create('employee_study_sessions', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('speaker');
                    $table->date('date');
                    $table->string('time_start', 10)->nullable();
                    $table->string('time_end', 10)->nullable();
                    $table->string('location')->default('Masjid SDIT Al-Fahmi');
                    $table->text('material_summary')->nullable();
                    $table->string('attachment_path')->nullable();
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->timestamps();

                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                    $table->index(['date']);
                });
            }

            if (!Schema::hasTable('employee_study_attendances')) {
                Schema::create('employee_study_attendances', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('session_id');
                    $table->unsignedBigInteger('user_id');
                    $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
                    $table->string('notes', 255)->nullable();
                    $table->timestamps();

                    $table->foreign('session_id')->references('id')->on('employee_study_sessions')->onDelete('cascade');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->unique(['session_id', 'user_id']);
                    $table->index(['session_id', 'status']);
                });
            }
        } catch (\Throwable $e) {
            // Handled
        }
    }
}
