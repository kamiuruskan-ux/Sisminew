<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkViolationCategory extends Model
{
    use HasFactory;

    protected $table = 'bk_violation_categories';

    protected $fillable = [
        'name',
        'level',
        'points',
        'penalty_recommendation',
        'description',
    ];

    public function violations()
    {
        return $this->hasMany(BkStudentViolation::class, 'violation_category_id');
    }
}
