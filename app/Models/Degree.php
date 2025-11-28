<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Degree extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'level',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => 'string',
        'level' => 'string',
    ];

    /**
     * Get the majors for the degree.
     */
    public function majors(): HasMany
    {
        return $this->hasMany(Major::class);
    }

    /**
     * Get the students for the degree.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function faculty(): BelongsTo {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Combines degree_level and degree_name (e.g., "Bachelor of Science").
     */
    public function getFullDegreeNameAttribute(): string
    {
        return "{$this->level} of {$this->name}";
    }

    /**
     * Scope a query to only include degrees by level.
     */
    public function scopeByLevel(Builder $query, string $level): void
    {
        $query->where('level', $level);
    }

    /**
     * Scope a query to only include degrees by name.
     */
    public function scopeByName(Builder $query, string $name): void
    {
        $query->where('name', 'like', '%' . $name . '%');
    }

    /**
     * Checks if the degree has associated majors.
     */
    public function hasMajors(): bool
    {
        return $this->majors()->exists();
    }
}
