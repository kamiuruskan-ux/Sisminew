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
        'sholat_fardhu',
        'rawatib_dhuha',
        'tilawah_quran',
        'dzikir_pagi_petang',
        'sholat_tahajud',
        'puasa_sunnah',
        'subuh_jamaah',
        'dzuhur_jamaah',
        'ashar_jamaah',
        'maghrib_jamaah',
        'isya_jamaah',
        'rawatib_count',
        'dhuha',
        'tahajjud_witir',
        'tilawah_pages',
        'sedekah',
        'daily_score',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'sholat_fardhu' => 'boolean',
        'rawatib_dhuha' => 'boolean',
        'tilawah_quran' => 'boolean',
        'dzikir_pagi_petang' => 'boolean',
        'sholat_tahajud' => 'boolean',
        'puasa_sunnah' => 'boolean',
        'subuh_jamaah' => 'boolean',
        'dzuhur_jamaah' => 'boolean',
        'ashar_jamaah' => 'boolean',
        'maghrib_jamaah' => 'boolean',
        'isya_jamaah' => 'boolean',
        'rawatib_count' => 'integer',
        'dhuha' => 'boolean',
        'tahajjud_witir' => 'boolean',
        'tilawah_pages' => 'integer',
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

        // Check if modern 6 amalan checklist is used
        $isFardhu = $this->sholat_fardhu || ($this->subuh_jamaah && $this->dzuhur_jamaah && $this->ashar_jamaah && $this->maghrib_jamaah && $this->isya_jamaah);
        $isRawatib = $this->rawatib_dhuha || ($this->rawatib_count >= 8 || $this->dhuha);
        $isTilawah = $this->tilawah_quran || ($this->tilawah_pages >= 4);
        $isDzikir = $this->dzikir_pagi_petang;
        $isTahajud = $this->sholat_tahajud || $this->tahajjud_witir;
        $isPuasa = $this->puasa_sunnah;

        // 4 Amalan Harian: 20 poin each (Total 80 poin)
        if ($isFardhu) $score += 20;
        if ($isRawatib) $score += 20;
        if ($isTilawah) $score += 20;
        if ($isDzikir) $score += 20;

        // 1 Amalan Pekanan: 10 poin
        if ($isTahajud) $score += 10;

        // 1 Amalan Bulanan: 10 poin
        if ($isPuasa) $score += 10;

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

                    $table->boolean('sholat_fardhu')->default(false);
                    $table->boolean('rawatib_dhuha')->default(false);
                    $table->boolean('tilawah_quran')->default(false);
                    $table->boolean('dzikir_pagi_petang')->default(false);
                    $table->boolean('sholat_tahajud')->default(false);
                    $table->boolean('puasa_sunnah')->default(false);

                    $table->boolean('subuh_jamaah')->default(false);
                    $table->boolean('dzuhur_jamaah')->default(false);
                    $table->boolean('ashar_jamaah')->default(false);
                    $table->boolean('maghrib_jamaah')->default(false);
                    $table->boolean('isya_jamaah')->default(false);

                    $table->unsignedTinyInteger('rawatib_count')->default(0);
                    $table->boolean('dhuha')->default(false);
                    $table->boolean('tahajjud_witir')->default(false);
                    
                    $table->unsignedSmallInteger('tilawah_pages')->default(0);

                    $table->boolean('sedekah')->default(false);

                    $table->decimal('daily_score', 5, 2)->default(0);
                    $table->text('notes')->nullable();

                    $table->timestamps();

                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->unique(['user_id', 'date'], 'employee_mutabaah_unique');
                    $table->index(['date', 'daily_score']);
                });
            } else {
                // Ensure new columns exist on existing table
                Schema::table('employee_mutabaahs', function (Blueprint $table) {
                    if (!Schema::hasColumn('employee_mutabaahs', 'sholat_fardhu')) {
                        $table->boolean('sholat_fardhu')->default(false)->after('date');
                    }
                    if (!Schema::hasColumn('employee_mutabaahs', 'rawatib_dhuha')) {
                        $table->boolean('rawatib_dhuha')->default(false)->after('sholat_fardhu');
                    }
                    if (!Schema::hasColumn('employee_mutabaahs', 'tilawah_quran')) {
                        $table->boolean('tilawah_quran')->default(false)->after('rawatib_dhuha');
                    }
                    if (!Schema::hasColumn('employee_mutabaahs', 'sholat_tahajud')) {
                        $table->boolean('sholat_tahajud')->default(false)->after('dzikir_pagi_petang');
                    }
                });
            }
        } catch (\Throwable $e) {
            // Silently continue
        }
    }
}
