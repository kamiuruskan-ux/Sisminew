<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LmsGamification extends Model
{
    protected $fillable = [
        'student_id',
        'xp',
        'level',
        'current_streak',
        'last_active_date',
        'badges',
    ];

    protected $casts = [
        'badges' => 'array',
        'last_active_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Add XP and calculate level
     */
    public function addXp(int $points, string $reason = '')
    {
        $this->xp += $points;
        
        // Level calculation: level = floor(xp / 500) + 1
        $newLevel = (int) floor($this->xp / 500) + 1;
        if ($newLevel > $this->level) {
            $this->level = $newLevel;
        }

        // Daily Streak Calculation
        $today = Carbon::today();
        if (!$this->last_active_date) {
            $this->current_streak = 1;
        } else {
            $diffInDays = $today->diffInDays($this->last_active_date);
            if ($diffInDays == 1) {
                $this->current_streak += 1;
            } elseif ($diffInDays > 1) {
                $this->current_streak = 1;
            }
        }
        $this->last_active_date = $today;

        // Check & Award Badges
        $badges = $this->badges ?? [];
        if (!in_array('first_step', $badges) && $this->xp >= 50) {
            $badges[] = 'first_step';
        }
        if (!in_array('lms_master', $badges) && $this->xp >= 1000) {
            $badges[] = 'lms_master';
        }
        if (!in_array('streak_3', $badges) && $this->current_streak >= 3) {
            $badges[] = 'streak_3';
        }
        $this->badges = $badges;

        $this->save();
        return $this;
    }

    public static function forStudent($studentId): self
    {
        return self::firstOrCreate(
            ['student_id' => $studentId],
            [
                'xp' => 0,
                'level' => 1,
                'current_streak' => 1,
                'last_active_date' => Carbon::today(),
                'badges' => ['first_step'],
            ]
        );
    }
}
