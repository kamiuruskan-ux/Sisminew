<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    public static function get(string $key, $default = null)
    {
        try {
            $setting = self::where('key', $key)->first();
            if ($setting && $setting->value !== null && $setting->value !== '') {
                return $setting->value;
            }

            // Fallback alias untuk kepala sekolah / pimpinan
            if (in_array($key, ['headmaster_name', 'principal_name', 'raport_principal_name', 'school_principal'])) {
                $alias = self::where('key', 'school_principal_name')->value('value');
                if ($alias) return $alias;
            } elseif (in_array($key, ['headmaster_nip', 'principal_nip', 'raport_principal_nip'])) {
                $alias = self::where('key', 'school_principal_nip')->value('value');
                if ($alias) return $alias;
            } elseif (in_array($key, ['headmaster_title', 'principal_title', 'raport_principal_title'])) {
                $alias = self::where('key', 'school_principal_title')->value('value');
                if ($alias) return $alias;
            }

            return $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function getPrincipalName(?string $default = null): ?string
    {
        return self::get('school_principal_name')
            ?: self::get('headmaster_name')
            ?: self::get('raport_principal_name')
            ?: self::get('principal_name')
            ?: self::get('school_principal')
            ?: $default;
    }

    public static function getPrincipalNip(?string $default = null): ?string
    {
        return self::get('school_principal_nip')
            ?: self::get('headmaster_nip')
            ?: self::get('raport_principal_nip')
            ?: self::get('principal_nip')
            ?: $default;
    }

    public static function getPrincipalTitle(?string $default = 'Kepala Sekolah'): string
    {
        return self::get('school_principal_title')
            ?: self::get('quran_raport_principal_title')
            ?: self::get('principal_title')
            ?: $default;
    }

    public static function set(string $key, $value): void
    {
        try {
            self::updateOrCreate(['key' => $key], ['value' => $value]);
        } catch (\Throwable $e) {
            // Abaikan jika database belum siap
        }
    }

    public static function getLogoUrl(): string
    {
        $logo = self::get('logo_path') ?? self::get('school_logo') ?? self::get('logo');
        if ($logo) {
            return get_public_file_url($logo, 'img');
        }
        if (\Illuminate\Support\Facades\File::exists(public_path('img/logo.png'))) {
            return asset('img/logo.png');
        }
        return asset('img/fav.png');
    }

    public static function getFaviconUrl(): string
    {
        $favicon = self::get('favicon_path') ?? self::get('school_favicon') ?? self::get('favicon');
        if ($favicon) {
            return get_public_file_url($favicon, 'img');
        }
        if (\Illuminate\Support\Facades\File::exists(public_path('img/fav.png'))) {
            return asset('img/fav.png');
        }
        return self::getLogoUrl();
    }

    public static function getLogoFilePath(): ?string
    {
        $candidates = [
            self::get('logo_path'),
            self::get('school_logo'),
            self::get('logo'),
            'img/logo.png',
            'img/fav.png',
        ];

        foreach ($candidates as $candidate) {
            if (!$candidate) continue;

            $clean = ltrim(str_replace('\\', '/', $candidate), '/');
            if (\Illuminate\Support\Str::startsWith($clean, 'system/public/')) {
                $clean = substr($clean, 14);
            }
            if (\Illuminate\Support\Str::startsWith($clean, 'storage/')) {
                $clean = substr($clean, 8);
            }

            $pathsToCheck = [
                public_path($clean),
                public_path('img/' . basename($clean)),
                base_path($clean),
                base_path('img/' . basename($clean)),
            ];

            foreach ($pathsToCheck as $p) {
                if (\Illuminate\Support\Facades\File::exists($p) && !\Illuminate\Support\Facades\File::isDirectory($p)) {
                    return $p;
                }
            }
        }

        return null;
    }

    public static function getLogoVersion(): string
    {
        $path = self::getLogoFilePath();
        if ($path && \Illuminate\Support\Facades\File::exists($path)) {
            return (string) \Illuminate\Support\Facades\File::lastModified($path);
        }
        return (string) time();
    }
}

