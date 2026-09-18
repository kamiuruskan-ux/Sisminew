<?php

namespace App\Support;

use App\Models\Setting as ModelsSetting;

class Setting
{
    /**
     * Get a setting value
     */
    public static function get(string $key, $default = null)
    {
        static $cache = [];
        
        if (!isset($cache[$key])) {
            $setting = ModelsSetting::where('key', $key)->first();
            $cache[$key] = $setting ? $setting->value : $default;
        }
        
        return $cache[$key];
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        ModelsSetting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Get logo URL helper
     */
    public static function getLogoUrl(): string
    {
        return ModelsSetting::getLogoUrl();
    }

    /**
     * Get favicon URL helper
     */
    public static function getFaviconUrl(): string
    {
        return ModelsSetting::getFaviconUrl();
    }

    /**
     * Forward any dynamic static method call to App\Models\Setting
     */
    public static function __callStatic($method, $parameters)
    {
        return ModelsSetting::$method(...$parameters);
    }
}
