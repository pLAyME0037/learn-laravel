<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability) {
        if ($user->isAdmin() || $user->isSuperUser()) { return true; }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool {
        if ($user->isAdmin() || $user->isSuperUser()) { return true; }
        return null;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool {
        if ($user->isAdmin() || $user->isSuperUser()) { return true; }
        return null;
    }
}
