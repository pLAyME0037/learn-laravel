<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'degree_id',
        'major_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'name' => 'string',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    // Relationship
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
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
}
