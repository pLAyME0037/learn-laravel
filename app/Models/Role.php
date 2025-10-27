<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Builder;

class Role extends SpatieRole
{
    use HasFactory;

    // Spatie's Role model has 'name' and 'guard_name' as fillable.
    // We add our custom columns from the migration.
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'is_system_role',
    ];

    protected $casts = [
        'is_system_role' => 'boolean',
    ];

    // The 'is_active' column and related scope are removed as they are not in the migration.
    // The custom permission logic is removed in favor of Spatie's built-in functionality.
    // The 'users' relationship is inherited from the parent SpatieRole model.

    public function scopeSystemRoles(Builder $query): void
    {
        $query->where('is_system_role', true);
    }

    /**
     * Returns "Yes" or "No" for `is_system_role`.
     */
    public function getIsSystemRoleDescriptionAttribute(): string
    {
        return $this->is_system_role ? 'Yes' : 'No';
    }

    /**
     * Scope a query to filter for roles that are not system roles.
     */
    public function scopeUserAssignable(Builder $query): void
    {
        $query->where('is_system_role', false);
    }

    /**
     * Checks if the role can be assigned to a user (i.e., not a system role).
     */
    public function assignableToUser(): bool
    {
        return !$this->is_system_role;
    }
}
