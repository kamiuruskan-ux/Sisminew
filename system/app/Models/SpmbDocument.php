<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbDocument extends Model
{
    protected $fillable = [
        'spmb_registration_id',
        'type',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'file_size' => 'integer',
    ];

    public function spmbRegistration(): BelongsTo
    {
        return $this->belongsTo(SpmbRegistration::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        return get_public_file_url($this->file_path, 'doc/spmb/documents');
    }
}
