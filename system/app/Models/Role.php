<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'guard_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const NON_ADMIN_SLUGS = [
        'student',
        'siswa',
        'calon-siswa',
        'spmb',
        'parent',
        'orangtua',
        'wali',
        'kantin',
        'canteen',
    ];

    public const PROTECTED_SYSTEM_SLUGS = [
        'admin',
        'guru',
        'guru-quran',
        'kepala-sekolah',
        'wakasek-kesiswaan',
        'wakasek-kurikulum',
        'wakasek-kehumasan',
        'bendahara',
        'operator',
        'staff',
        'guru-bk',
        'superadmin',
        'super-admin',
        'super_admin',
    ];

    public function scopeAdminOnly($query)
    {
        return $query->whereNotIn('slug', self::NON_ADMIN_SLUGS);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->slug, ['superadmin', 'super-admin', 'super_admin']);
    }

    public function isProtectedSystemRole(): bool
    {
        return in_array($this->slug, self::PROTECTED_SYSTEM_SLUGS);
    }

    public function isNonAdminRole(): bool
    {
        return in_array($this->slug, self::NON_ADMIN_SLUGS);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
    }

    public function hasPermission(string $permission): bool
    {
        if (str_contains($permission, '|')) {
            foreach (explode('|', $permission) as $p) {
                if ($this->hasPermission(trim($p))) {
                    return true;
                }
            }
            return false;
        }

        if ($this->permissions->contains('slug', $permission)) {
            return true;
        }

        if (str_starts_with($permission, 'view-')) {
            $managePerm = 'manage-' . substr($permission, 5);
            return $this->permissions->contains('slug', $managePerm);
        }

        return false;
    }

    public function givePermission(Permission $permission): void
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions()->attach($permission);
        }
    }

    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission);
    }
}
