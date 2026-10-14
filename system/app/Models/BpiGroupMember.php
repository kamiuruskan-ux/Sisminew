<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BpiGroupMember extends Model
{
    use HasFactory;

    protected $table = 'bpi_group_members';

    protected $fillable = [
        'bpi_group_id',
        'student_id',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(BpiGroup::class, 'bpi_group_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
