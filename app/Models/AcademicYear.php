<?php

declare (strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'start_date', 'end_date', 'is_current'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    /**
     * Get the formatted year range (e.g., "2023-2024").
     */
    public function getYearRangeAttribute(): string
    {
        if (! $this->start_date || ! $this->end_date) {
            return 'N/A';
        }
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
