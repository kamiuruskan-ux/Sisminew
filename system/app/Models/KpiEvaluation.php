<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class KpiEvaluation extends Model
{
    use HasFactory;

    protected $table = 'kpi_evaluations';

    protected $fillable = [
        'user_id',
        'evaluator_id',
        'period_type',
        'period_year',
        'period_month',
        'period_semester',
        'score_comp_1',
        'score_comp_2',
        'score_comp_3',
        'score_comp_4',
        'score_comp_5',
        'final_score',
        'predicate',
        'attendance_percentage',
        'gate_passed',
        'is_capped',
        'feedback_appreciation',
        'feedback_improvement',
        'status',
        'published_at',
    ];

    protected $casts = [
        'score_comp_1' => 'float',
        'score_comp_2' => 'float',
        'score_comp_3' => 'float',
        'score_comp_4' => 'float',
        'score_comp_5' => 'float',
        'final_score' => 'float',
        'attendance_percentage' => 'float',
        'gate_passed' => 'boolean',
        'is_capped' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(KpiEvaluationItem::class, 'kpi_evaluation_id');
    }

    /**
     * Predicate styling and labels
     */
    public function getPredicateLabelAttribute(): string
    {
        return match ($this->predicate) {
            'A' => 'Sangat Baik (Mumtaz)',
            'B' => 'Baik (Jayyid Jiddan)',
            'C' => 'Cukup (Maqbul)',
            'D' => 'Kurang (Dhaif)',
            default => 'Cukup',
        };
    }

    public function getPredicateBadgeClassAttribute(): string
    {
        return match ($this->predicate) {
            'A' => 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border-emerald-300/50',
            'B' => 'bg-indigo-100 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 border-indigo-300/50',
            'C' => 'bg-amber-100 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border-amber-300/50',
            'D' => 'bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 border-rose-300/50',
            default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300/50',
        };
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if ($this->period_type === 'month' && !empty($this->period_month)) {
            return ($months[$this->period_month] ?? 'Bulan ' . $this->period_month) . ' ' . $this->period_year;
        } elseif ($this->period_type === 'semester') {
            return 'Semester ' . ($this->period_semester ?? 1) . ' TA ' . $this->period_year;
        }

        return 'Tahun ' . $this->period_year;
    }

    protected static function booted()
    {
        static::ensureTablesExist();
    }

    public static function ensureTablesExist(): void
    {
        try {
            if (!Schema::hasTable('kpi_evaluations')) {
                Schema::create('kpi_evaluations', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('evaluator_id')->nullable();
                    $table->string('period_type', 20)->default('month');
                    $table->unsignedSmallInteger('period_year');
                    $table->unsignedTinyInteger('period_month')->nullable();
                    $table->unsignedTinyInteger('period_semester')->nullable();
                    
                    $table->decimal('score_comp_1', 5, 2)->default(0);
                    $table->decimal('score_comp_2', 5, 2)->default(0);
                    $table->decimal('score_comp_3', 5, 2)->default(0);
                    $table->decimal('score_comp_4', 5, 2)->default(0);
                    $table->decimal('score_comp_5', 5, 2)->default(0);

                    $table->decimal('final_score', 5, 2)->default(0);
                    $table->string('predicate', 10)->default('C');
                    $table->decimal('attendance_percentage', 5, 2)->default(0);
                    $table->boolean('gate_passed')->default(true);
                    $table->boolean('is_capped')->default(false);

                    $table->text('feedback_appreciation')->nullable();
                    $table->text('feedback_improvement')->nullable();
                    $table->string('status', 20)->default('draft');
                    $table->timestamp('published_at')->nullable();

                    $table->timestamps();

                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('evaluator_id')->references('id')->on('users')->onDelete('set null');
                    $table->unique(['user_id', 'period_year', 'period_month', 'period_type'], 'kpi_user_period_unique');
                    $table->index(['period_year', 'period_month']);
                    $table->index(['predicate', 'final_score']);
                });
            }

            if (!Schema::hasTable('kpi_evaluation_items')) {
                Schema::create('kpi_evaluation_items', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('kpi_evaluation_id');
                    $table->string('indicator_code', 30);
                    $table->decimal('score', 5, 2)->default(0);
                    $table->string('source_type', 20)->default('auto');
                    $table->string('source_detail', 255)->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();

                    $table->foreign('kpi_evaluation_id')->references('id')->on('kpi_evaluations')->onDelete('cascade');
                    $table->unique(['kpi_evaluation_id', 'indicator_code'], 'kpi_item_unique');
                });
            }
        } catch (\Throwable $e) {
            // Silently continue if already created
        }
    }
}
