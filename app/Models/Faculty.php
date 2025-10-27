<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'faculty_name',
    ];

    /**
     * Get the departments for the faculty.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the programs for the faculty.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Scope a query to only include faculties by name.
     */
    public function scopeByName(Builder $query, string $name): void
    {
        $query->where('faculty_name', 'like', '%' . $name . '%');
    }

    /**
     * Checks if the faculty has associated departments.
     */
    public function hasDepartments(): bool
    {
        return $this->departments()->exists();
    }
}
