<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemConfig extends Model
{
    protected $fillable = ['key', 'value'];

    const CACHE_KEY = 'system_configs_all';

    /**
     * Boot: Clear cache automatically.
     */
    protected static function booted()
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Get a config value.
     * Usage: SystemConfig::get('site_name', 'My University');
     */
    public static function get(string $key, $default = null)
    {
        $configs = Cache::rememberForever(self::CACHE_KEY, function () {
            return self::all()->pluck('value', 'key');
        });

        return $configs[$key] ?? $default;
    }

    /**
     * Set a config value (Create or Update).
     * Usage: SystemConfig::set('maintenance_mode', 'true');
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
    
    /**
     * Helper checks for boolean values stored as text.
     */
    public static function isTrue(string $key): bool 
    {
        $val = self::get($key);
        return $val === '1' || $val === 'true' || $val === true;
    }
}