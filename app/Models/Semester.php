<?php

declare (strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function academicYear(): BelongsTo {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classSchedules(): HasMany {
        return $this->hasMany(ClassSchedule::class);
    }

    public function courses(): HasMany {
        return $this->hasMany(Course::class);
    }

    /**
     * Returns a formatted string like "Fall 2023 (Sep 1 - Dec 15)".
     */
    public function getSemesterPeriodAttribute(): string
    {
        return "{$this->name} {$this->academicYear->name} ({$this->start_date->format('M j')} - {$this->end_date->format('M j')})";
    }

    /**
     * Scope a query to filter for the current semester.
     */
    public function scopeCurrent(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to filter semesters by academic year.
     */
    public function scopeByAcademicYear(Builder $query, int $academicYearId): void
    {
        $query->where('academic_year_id', $academicYearId);
    }
    /**
     * Scope a query to filter for semesters that are currently active.
     */
    public function scopeActive(Builder $query): void
    {
        $now = Carbon::now();
        $query->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now);
    }

    /**
     * Checks if the semester is active based on dates.
     */
    public function isCurrentlyActive(): bool
    {
        $now = Carbon::now();
        return $this->start_date->lte($now) && $this->end_date->gte($now);
    }

    /**
     * Returns the number of courses offered in this semester.
     */
    public function getCourseCount(): int
    {
        return $this->courses()->count();
    }
}
