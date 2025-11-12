<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gender extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the students for the gender.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Scope a query to only include genders by name.
     */
    public function scopeByName(Builder $query, string $name): void
    {
        $query->where('name', 'like', '%' . $name . '%');
    }
}
