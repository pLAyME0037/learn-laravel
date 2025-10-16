<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

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

    // The 'is_active' column and related scope are removed as they are not in the migration.
    // The custom permission logic is removed in favor of Spatie's built-in functionality.
    // The 'users' relationship is inherited from the parent SpatieRole model.

    public function scopeSystemRoles($query)
    {
        return $query->where('is_system_role', true);
    }
}
