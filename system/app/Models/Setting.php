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
            return $setting ? $setting->value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
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
        if (\Illuminate\Support\Facades\File::exists(public_path('img/fav.png'))) {
            return asset('img/fav.png');
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
}
