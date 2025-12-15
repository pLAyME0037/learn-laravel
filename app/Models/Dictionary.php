<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Dictionary extends Model
{
    use HasFactory;

    protected $fillable = ['category', 'key', 'label', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot: Clear cache automatically when modified.
     */
    protected static function booted()
    {
        static::saved(fn ($dictionary) => self::clearCache($dictionary->category));
        static::deleted(fn ($dictionary) => self::clearCache($dictionary->category));
    }

    /**
     * Helper: Get simple array [key => label] for Dropdowns.
     * Usage: Dictionary::options('gender');
     */
    public static function options(string $category): array
    {
        return Cache::rememberForever("dictionary_{$category}", function () use ($category) {
            return self::where('category', $category)
                ->where('is_active', true)
                ->orderBy('label') // or orderBy('id') to keep insertion order
                ->pluck('label', 'key')
                ->toArray();
        });
    }

    /**
     * Helper: Get the Label for a specific key.
     * Usage: Dictionary::label('gender', 'm'); // returns "Male"
     */
    public static function label(string $category, string $key): string
    {
        $options = self::options($category);
        return $options[$key] ?? $key; // Return key if label not found
    }

    private static function clearCache(string $category): void
    {
        Cache::forget("dictionary_{$category}");
    }
}