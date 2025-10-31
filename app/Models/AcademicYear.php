<?php

declare (strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_current'];

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    /**
     * Cast the start_date attribute.
     */
    protected function startDate(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => new Carbon($value),
            set: fn(Carbon $value) => $value,
        );
    }

    /**
     * Cast the end_date attribute.
     */
    protected function endDate(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => new Carbon($value),
            set: fn(Carbon $value) => $value,
        );
    }

    /**
     * Cast the is_current attribute.
     */
    protected function isCurrent(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => (bool) $value,
            set: fn(bool $value)   => $value,
        );
    }

    /**
     * Get the formatted year range (e.g., "2023-2024").
     */
    public function getYearRangeAttribute(): string
    {
        return "{$this->start_date->format('Y')}-{$this->end_date->format('Y')}";
    }

    /**
     * Scope a query to only include the current academic year.
     */
    public function scopeCurrent(Builder $query): void
    {
        $query->where('is_current', true);
    }

    /**
     * Scope a query to only include academic years that are currently active.
     */
    public function scopeActive(Builder $query): void
    {
        $now = Carbon::now();
        $query->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now);
    }

    /**
     * Checks if the academic year is active based on dates.
     */
    public function isCurrentlyActive(): bool
    {
        $now = Carbon::now();
        return $this->start_date->lte($now) && $this->end_date->gte($now);
    }
}
