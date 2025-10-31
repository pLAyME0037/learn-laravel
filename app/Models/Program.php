<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Program extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'department_id',
        'name',
        'code',
        'description',
        'level',
        'duration_years',
        'total_semesters',
        'total_credits_required',
        'tuition_fee',
        'curriculum',
        'is_active',
    ];

    protected $casts = [
        'tuition_fee' => 'decimal:2',
        'curriculum'  => 'array',
        'is_active'   => 'boolean',
    ];

    // Relationship
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the majors for the program.
     */
    public function majors(): HasMany
    {
        return $this->hasMany(Major::class);
    }

    // Accessors
    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }
    public function getActiveStudentCountAttribute(): int
    {
        return $this->students()->active()->count();
    }
    public function getFullNameAttribute(): string
    {
        return "{$this->name} - {$this->code}";
    }

    /**
     * Returns the tuition fee formatted as currency.
     */
    public function getFormattedTuitionFeeAttribute(): string
    {
        return '$' . number_format($this->tuition_fee, 2);
    }

    /**
     * Returns a human-readable duration (e.g., "4 Years, 8 Semesters").
     */
    public function getDurationDescriptionAttribute(): string
    {
        return "{$this->duration_years} Years, {$this->total_semesters} Semesters";
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
    public function scopeByDepartment(Builder $query, int $departmentId): void
    {
        $query->where('department_id', $departmentId);
    }
    public function scopeByLevel(Builder $query, string $level): void
    {
        $query->where('level', $level);
    }

    /**
     * Scope a query to filter programs by duration in years.
     */
    public function scopeByDuration(Builder $query, int $years): void
    {
        $query->where('duration_years', $years);
    }

    /**
     * Scope a query to filter programs with tuition less than a specified amount.
     */
    public function scopeTuitionLessThan(Builder $query, float $amount): void
    {
        $query->where('tuition_fee', '<', $amount);
    }

    // Helper Methods
    public function canDelete(): bool
    {
        return $this->students()->count() === 0 && $this->courses()->count() === 0 && $this->majors()->count() === 0;
    }

    /**
     * Adds a course to the program.
     */
    public function addCourse(Course $course): void
    {
        $this->courses()->save($course);
    }

    /**
     * Removes a course from the program.
     */
    public function removeCourse(Course $course): void
    {
        $this->courses()->where('id', $course->id)->delete();
    }

    /**
     * Calculates the total credits for the program.
     */
    public function getTotalCredits(): int
    {
        return $this->total_credits_required;
    }
}
