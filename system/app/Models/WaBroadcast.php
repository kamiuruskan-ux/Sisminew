<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaBroadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'target_type',
        'target_class_id',
        'message',
        'status',
        'total_recipients',
        'success_count',
        'failed_count',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function targetClass()
    {
        return $this->belongsTo(ClassModel::class, 'target_class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(WaBroadcastLog::class, 'wa_broadcast_id');
    }

    public function getTargetTypeLabelAttribute(): string
    {
        return match ($this->target_type) {
            'all_students' => 'Semua Siswa',
            'all_parents' => 'Semua Orang Tua / Wali',
            'all_teachers' => 'Semua Guru / Staff',
            'class' => 'Kelas ' . ($this->targetClass->name ?? ''),
            'spmb' => 'Calon Siswa (SPMB)',
            'custom' => 'Kontak Custom (Manual)',
            default => ucfirst($this->target_type),
        };
    }
}
