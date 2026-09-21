<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class EmployeeMutabaah extends Model
{
    use HasFactory;

    protected $table = 'employee_mutabaahs';

    protected $fillable = [
        'user_id',
        'date',
        'subuh_jamaah',
        'dzuhur_jamaah',
        'ashar_jamaah',
        'maghrib_jamaah',
        'isya_jamaah',
        'rawatib_count',
        'dhuha',
        'tahajjud_witir',
        'tilawah_pages',
        'dzikir_pagi_petang',
        'puasa_sunnah',
        'sedekah',
        'daily_score',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'subuh_jamaah' => 'boolean',
        'dzuhur_jamaah' => 'boolean',
        'ashar_jamaah' => 'boolean',
        'maghrib_jamaah' => 'boolean',
        'isya_jamaah' => 'boolean',
        'rawatib_count' => 'integer',
        'dhuha' => 'boolean',
        'tahajjud_witir' => 'boolean',
        'tilawah_pages' => 'integer',
        'dzikir_pagi_petang' => 'boolean',
        'puasa_sunnah' => 'boolean',
        'sedekah' => 'boolean',
        'daily_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Compute daily mutabaah score out of 100
     */
    public function computeDailyScore(): float
    {
        $score = 0;

        // 1. Shalat 5 Waktu Berjamaah (40 poin, @ 8 poin)
        if ($this->subuh_jamaah) $score += 8;
        if ($this->dzuhur_jamaah) $score += 8;
        if ($this->ashar_jamaah) $score += 8;
        if ($this->maghrib_jamaah) $score += 8;
        if ($this->isya_jamaah) $score += 8;

        // 2. Rawatib (target 10-12 rakaat: 15 poin)
        $rawatib = max(0, min(12, (int)$this->rawatib_count));
        $score += min(15, ($rawatib / 10) * 15);

        // 3. Dhuha (10 poin)
        if ($this->dhuha) $score += 10;

        // 4. Tahajjud / Witir (15 poin)
        if ($this->tahajjud_witir) $score += 15;

        // 5. Tilawah Qur'an (min 2 lembar / 4 halaman: 10 poin)
        $pages = max(0, (int)$this->tilawah_pages);
        $score += min(10, ($pages / 4) * 10);

        // 6. Dzikir Pagi Petang (5 poin)
        if ($this->dzikir_pagi_petang) $score += 5;

        // 7. Sedekah Harian (5 poin)
        if ($this->sedekah) $score += 5;

        // 8. Puasa Sunnah (Bonus +5 jika ada, capped at 100)
        if ($this->puasa_sunnah) $score += 5;

        return min(100, round($score, 2));
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->daily_score = $model->computeDailyScore();
        });

        static::ensureTablesExist();
    }

    public static function ensureTablesExist(): void
    {
        try {
            if (!Schema::hasTable('employee_mutabaahs')) {
                Schema::create('employee_mutabaahs', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->date('date');

                    $table->boolean('subuh_jamaah')->default(false);
                    $table->boolean('dzuhur_jamaah')->default(false);
                    $table->boolean('ashar_jamaah')->default(false);
                    $table->boolean('maghrib_jamaah')->default(false);
                    $table->boolean('isya_jamaah')->default(false);

                    $table->unsignedTinyInteger('rawatib_count')->default(0);
                    $table->boolean('dhuha')->default(false);
                    $table->boolean('tahajjud_witir')->default(false);
                    
                    $table->unsignedSmallInteger('tilawah_pages')->default(0);
                    $table->boolean('dzikir_pagi_petang')->default(false);

                    $table->boolean('puasa_sunnah')->default(false);
                    $table->boolean('sedekah')->default(false);

                    $table->decimal('daily_score', 5, 2)->default(0);
                    $table->text('notes')->nullable();

                    $table->timestamps();

                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->unique(['user_id', 'date'], 'employee_mutabaah_unique');
                    $table->index(['date', 'daily_score']);
                });
            }
        } catch (\Throwable $e) {
            // Silently continue
        }
    }
}
