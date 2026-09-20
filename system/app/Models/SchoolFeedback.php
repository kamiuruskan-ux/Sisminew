<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SchoolFeedback extends Model
{
    protected $table = 'school_feedbacks';

    protected $fillable = [
        'user_id',
        'category',
        'content',
        'is_anonymous',
        'author_name',
        'author_role',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return match($this->category) {
            'kritik' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
            'saran' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800',
            'masukan' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
            default => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'baru' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800',
            'dibahas' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
            'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
            default => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }

    protected static function booted()
    {
        static::ensureTableExists();
    }

    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('school_feedbacks')) {
                Schema::create('school_feedbacks', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->string('category', 20)->default('saran');
                    $table->text('content');
                    $table->boolean('is_anonymous')->default(false);
                    $table->string('author_name')->nullable();
                    $table->string('author_role')->nullable();
                    $table->string('status', 20)->default('baru');
                    $table->text('admin_notes')->nullable();
                    $table->timestamps();

                    $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                    $table->index(['category', 'status']);
                    $table->index(['created_at']);
                });
            }
        } catch (\Throwable $e) {
            // Handled
        }
    }
}
