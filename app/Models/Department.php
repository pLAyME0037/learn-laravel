<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'hod_id',
        'email',
        'phone',
        'office_location',
        'established_year',
        'budget',
        'is_active',
        'metadata',
        'faculty_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'budget' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationship
    public function hod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the courses for the department.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the instructors for the department.
     */
    public function instructors(): HasMany
    {
        return $this->hasMany(Instructor::class);
    }

    /**
     * Get the transaction ledgers for the department.
     */
    public function transactionLedgers(): HasMany
    {
        return $this->hasMany(TransactionLedger::class);
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
    public function scopeWithHod(Builder $query): void
    {
        $query->with('hod');
    }
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Scope a query to filter departments by established year.
     */
    public function scopeByEstablishedYear(Builder $query, int $year): void
    {
        $query->where('established_year', $year);
    }

    /**
     * Scope a query to filter departments with a budget greater than a specified amount.
     */
    public function scopeHasBudgetGreaterThan(Builder $query, float $amount): void
    {
        $query->where('budget', '>', $amount);
    }

    // Accessors
    public function getStaffCountAttribute(): int
    {
        return $this->users()->staff()->count();
    }
    public function getStudentCountAttribute(): int
    {
        return $this->users()->students()->count();
    }
    public function getProgramCountAttribute(): int
    {
        return $this->programs()->count();
    }

    /**
     * Returns the budget formatted as currency.
     */
    public function getFormattedBudgetAttribute(): string
    {
        return '$' . number_format($this->budget, 2);
    }

    /**
     * Combines email and phone for easy display.
     */
    public function getContactInfoAttribute(): string
    {
        return "Email: {$this->email}, Phone: {$this->phone}";
    }

    // Helper Methods
    public function canDelete(): bool
    {
        return $this->users()->count() === 0 && $this->programs()->count() === 0 && $this->courses()->count() === 0 && $this->instructors()->count() === 0;
    }

    /**
     * Adds a program to the department.
     */
    public function addProgram(Program $program): void
    {
        $this->programs()->save($program);
    }

    /**
     * Removes a program from the department.
     */
    public function removeProgram(Program $program): void
    {
        $this->programs()->where('id', $program->id)->delete();
    }

    /**
     * Calculates total budget spent (requires TransactionLedger relationship).
     */
    public function getTotalBudgetSpent(): float
    {
        return $this->transactionLedgers()->sum('debit');
    }
}
