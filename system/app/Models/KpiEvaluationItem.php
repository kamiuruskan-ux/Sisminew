<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiEvaluationItem extends Model
{
    use HasFactory;

    protected $table = 'kpi_evaluation_items';

    protected $fillable = [
        'kpi_evaluation_id',
        'indicator_code',
        'score',
        'source_type',
        'source_detail',
        'notes',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(KpiEvaluation::class, 'kpi_evaluation_id');
    }
}
