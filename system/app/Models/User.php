<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'nip',
        'tmt',
        'last_education',
        'avatar',
        'status',
        'face_embedding',
        'face_photo',
        'face_registered_at',
        'fingerprint_template',
        'fingerprint_registered_at',
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'tmt' => 'date',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isLockedOut(): bool
    {
        return $this->locked_until !== null && now()->lessThan($this->locked_until);
    }

    public function getLockoutRemainingSeconds(): int
    {
        if (!$this->isLockedOut()) {
            return 0;
        }
        return (int) max(0, now()->diffInSeconds($this->locked_until));
    }

    public function unlockAccount(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function recordLoginSuccess(?string $ip = null): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Get the email address for password reset.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $schoolName = Setting::get('school_name', config('app.name'));
        $this->notify(new ResetPasswordNotification($token, $schoolName));
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function teacherAttendances(): HasMany
    {
        return $this->hasMany(TeacherAttendance::class, 'user_id');
    }


    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return $this->roles->pluck('slug')->intersect($role)->isNotEmpty();
        }
        return $this->roles->contains('slug', $role);
    }

    public function assignRole(Role $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles()->attach($role);
        }
    }

    public function removeRole(Role $role): void
    {
        $this->roles()->detach($role);
    }

    public function syncRoles(array $roles): void
    {
        $roleIds = collect($roles)->map(function($role) {
            return $role instanceof Role ? $role->id : $role;
        })->toArray();
        
        $this->roles()->sync($roleIds);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        if (str_contains($permission, '|')) {
            foreach (explode('|', $permission) as $p) {
                if ($this->hasPermission(trim($p))) {
                    return true;
                }
            }
            return false;
        }

        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        if (str_starts_with($permission, 'view-')) {
            $managePerm = 'manage-' . substr($permission, 5);
            foreach ($this->roles as $role) {
                if ($role->hasPermission($managePerm)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    public function verifiedSpmbRegistrations(): HasMany
    {
        return $this->hasMany(SpmbRegistration::class, 'verified_by');
    }

    public function spmbRegistrations(): HasMany
    {
        return $this->hasMany(SpmbRegistration::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super-admin');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('guru') || $this->hasRole('teacher');
    }

    public function scopeRole($query, string $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('slug', $role);
        });
    }

    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(ClassModel::class, 'homeroom_teacher_id');
    }

    public function quranClasses(): BelongsToMany
    {
        return $this->belongsToMany(ClassModel::class, 'quran_teacher_classes', 'user_id', 'class_id')->withTimestamps();
    }

    public function getMasaKerjaAttribute(): string
    {
        if (!$this->tmt) {
            return '-';
        }

        $start = \Carbon\Carbon::parse($this->tmt);
        $diff = $start->diff(now());

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = $diff->y . ' Tahun';
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m . ' Bulan';
        }

        if (empty($parts)) {
            return '< 1 Bulan';
        }

        return implode(' ', $parts);
    }

    protected static function booted()
    {
        static::ensureEmploymentSchemaExists();
    }

    public static function ensureEmploymentSchemaExists(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'tmt')) {
                    \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                        $table->date('tmt')->nullable()->after('nip');
                    });
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_education')) {
                    \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                        $table->string('last_education', 100)->nullable()->after('tmt');
                    });
                }
            }

            if (!\Illuminate\Support\Facades\Schema::hasTable('quran_teacher_classes')) {
                \Illuminate\Support\Facades\Schema::create('quran_teacher_classes', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('class_id');
                    $table->timestamps();

                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
                    $table->unique(['user_id', 'class_id']);
                });
            }
        } catch (\Throwable $e) {
            // Handled
        }
    }

    public function getAvatarUrlAttribute(): string
    {
        if (empty($this->avatar)) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=ffffff&size=256';
        }
        return get_public_file_url($this->avatar, 'img/avatars');
    }
}
