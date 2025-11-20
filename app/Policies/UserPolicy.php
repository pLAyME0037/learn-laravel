<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Grant all permissions to Super Administrators.
     */
    public function before(User $user, $ability): ?bool
    {
        if ($user->hasRole('Super Administrator')) {
            return true;
        }

        return null; // Let other methods decide
    }

    public function view(User $currentUser, User $targetUser): bool
    {
        return $currentUser->hasPermissionTo('view.users');
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.users');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create.users');
    }

    public function update(User $currentUser, User $targetUser): bool
    {
        return $currentUser->hasPermissionTo('edit.users');
    }

    public function updateStatus(User $currentUser, User $targetUser): bool
    {
        // Prevent users from deactivating themselves
        if ($currentUser->id === $targetUser->id) {
            return false;
        }
        return $currentUser->hasPermissionTo('edit.users');
    }

    public function changePassword(User $currentUser, User $targetUser): bool
    {
        if ($currentUser->hasRole('admin')) {
            return true;
        }

        // HODs can change passwords for users who are not admins or Super Admins
        if (
            $currentUser->hasRole('hod') 
            && ! $targetUser->hasAnyRole(['admin', 'Super Administrator'])) {
            return true;
        }

        return false;
    }

    public function delete(User $currentUser, User $targetUser): bool
    {
        // Prevent users from deleting themselves
        if ($currentUser->id === $targetUser->id) {
            return false;
        }
        return $currentUser->hasPermissionTo('delete.users');
    }

    public function restore(User $currentUser, User $targetUser): bool
    {
        return $currentUser->hasPermissionTo('delete.users');
    }

    public function forceDelete(User $currentUser, User $targetUser): bool
    {
        // Prevent users from permanently deleting themselves
        if ($currentUser->id === $targetUser->id) {
            return false;
        }
        return $currentUser->hasPermissionTo('delete.users');
    }
}
