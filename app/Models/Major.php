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

class Major extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'major_name',
        'department_id',
        'degree_id',
        'major_cost',
        'payment_frequency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'major_cost' => 'decimal:2',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }

    /**
     * Get the students for the major.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * The programs that belong to the Major.
     */
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_major_pivot', 'major_id', 'program_id');
    }

    /**
     * Returns the major cost formatted as currency.
     */
    public function getFormattedMajorCostAttribute(): string
    {
        return '$' . number_format($this->major_cost, 2);
    }

    /**
     * Returns a human-readable description of the payment frequency.
     */
    public function getPaymentFrequencyDescriptionAttribute(): string
    {
        return ucfirst($this->payment_frequency);
    }

    /**
     * Scope a query to only include majors by department.
     */
    public function scopeByDepartment(Builder $query, int $departmentId): void
    {
        $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to only include majors by degree.
     */
    public function scopeByDegree(Builder $query, int $degreeId): void
    {
        $query->where('degree_id', $degreeId);
    }

    /**
     * Scope a query to filter majors with a cost greater than a specified amount.
     */
    public function scopeCostGreaterThan(Builder $query, float $amount): void
    {
        $query->where('major_cost', '>', $amount);
    }

    /**
     * Checks if there are any students associated with this major.
     */
    public function hasStudents(): bool
    {
        return $this->students()->exists();
    }
}
