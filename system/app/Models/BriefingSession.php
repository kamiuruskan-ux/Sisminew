<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class BriefingSession extends Model
{
    protected $table = 'briefing_sessions';

    protected $fillable = [
        'date',
        'title',
        'notes',
        'is_active',
        'opened_by',
        'start_time',
        'end_time',
        'closed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'closed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(BriefingAttendance::class, 'session_id');
    }

    /**
     * Cek apakah sesi briefing saat ini aktif dan belum melewati batas waktu tutup
     */
    public function isOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = date('Y-m-d');
        $sessionDate = is_string($this->date) ? substr($this->date, 0, 10) : $this->date->format('Y-m-d');
        if ($sessionDate !== $today) {
            return false;
        }

        if (!empty($this->end_time)) {
            $nowTime = now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i');
            $endTime = substr($this->end_time, 0, 5);
            if ($nowTime > $endTime) {
                return false;
            }
        }

        return true;
    }

    /**
     * Cek apakah waktu sesi telah melewati batas jam tutup
     */
    public function isExpired(): bool
    {
        if (empty($this->end_time)) {
            return false;
        }

        $nowTime = now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i');
        $endTime = substr($this->end_time, 0, 5);
        return $nowTime > $endTime;
    }

    /**
     * Hitung sisa menit sebelum sesi briefing ditutup
     */
    public function getRemainingMinutesAttribute(): int
    {
        if (empty($this->end_time)) {
            return 0;
        }

        $now = now()->setTimezone(config('app.timezone', 'Asia/Makassar'));
        $end = \Carbon\Carbon::createFromFormat('H:i', substr($this->end_time, 0, 5), config('app.timezone', 'Asia/Makassar'));

        $diff = $now->diffInMinutes($end, false);
        return max(0, (int)$diff);
    }

    protected static function booted()
    {
        static::ensureTablesExist();
    }

    public static function ensureTablesExist(): void
    {
        try {
            if (!Schema::hasTable('briefing_sessions')) {
                Schema::create('briefing_sessions', function (Blueprint $table) {
                    $table->id();
                    $table->date('date');
                    $table->string('title')->default('Briefing Rutin Harian Pegawai & Evaluasi');
                    $table->text('notes')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->unsignedBigInteger('opened_by')->nullable();
                    $table->string('start_time', 10)->nullable();
                    $table->string('end_time', 10)->nullable();
                    $table->timestamp('closed_at')->nullable();
                    $table->timestamps();

                    $table->foreign('opened_by')->references('id')->on('users')->onDelete('set null');
                    $table->index(['date', 'is_active']);
                });
            }

            if (!Schema::hasTable('briefing_attendances')) {
                Schema::create('briefing_attendances', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('session_id');
                    $table->unsignedBigInteger('user_id');
                    $table->date('date');
                    $table->string('attended_at', 10)->nullable();
                    $table->string('device_info', 255)->nullable();
                    $table->decimal('latitude', 10, 8)->nullable();
                    $table->decimal('longitude', 11, 8)->nullable();
                    $table->string('status', 30)->default('hadir');
                    $table->string('notes', 255)->nullable();
                    $table->timestamps();

                    $table->foreign('session_id')->references('id')->on('briefing_sessions')->onDelete('cascade');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->unique(['session_id', 'user_id']);
                    $table->index(['date', 'user_id']);
                });
            }
        } catch (\Throwable $e) {
            // Handled
        }
    }
}
