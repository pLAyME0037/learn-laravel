<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SystemConfig extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Cast the value attribute.
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value, true) ?? $value,
            set: fn (mixed $value) => is_array($value) ? json_encode($value) : $value,
        );
    }

    /**
     * Scope a query to filter configurations by key.
     */
    public function scopeByKey(Builder $query, string $key): void
    {
        $query->where('key', $key);
    }

    /**
     * Static helper to easily retrieve a setting.
     */
    public static function getSetting(string $key, mixed $default = null): mixed
    {
        $config = static::byKey($key)->first();
        if ($config) {
            return $config->value;
        }
        return $default;
    }

    /**
     * Static helper to easily set a setting.
     */
    public static function setSetting(string $key, mixed $value): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
