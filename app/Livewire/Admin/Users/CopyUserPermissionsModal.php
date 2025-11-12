<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use LivewireUI\Modal\Contracts\ModalComponent;

class CopyUserPermissionsModal extends Component implements ModalComponent
{
    public $userId;
    public $sourceType = 'role'; // 'role' or 'user'
    public array $selectedSourceIds = [];
    public $users;
    public $roles;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->users = User::where('id', '!=', $userId)->get();
        $this->roles = Role::all();
        $this->selectedSourceIds = [];
    }

    public function copyPermissions()
    {
        $targetUser = User::find($this->userId);
        $allPermissionsToCopy = collect();

        foreach ($this->selectedSourceIds as $sourceId) {
            if ($this->sourceType === 'role') {
                $sourceRole = Role::findById($sourceId);
                if ($sourceRole) {
                    $allPermissionsToCopy = $allPermissionsToCopy->merge(
                        $sourceRole->permissions->pluck('name')
                    );
                }
            } elseif ($this->sourceType === 'user') {
                $sourceUser = User::find($sourceId);
                if ($sourceUser) {
                    $allPermissionsToCopy = $allPermissionsToCopy->merge(
                        $sourceUser->permissions->pluck('name')
                    );
                }
            }
        }

        $uniquePermissionsToCopy = $allPermissionsToCopy->unique()->toArray();

        if ($targetUser && !empty($uniquePermissionsToCopy)) {
            $targetUser->syncPermissions($uniquePermissionsToCopy);
            session()->flash('success', 'Permissions copied successfully!');
            $this->closeModal();
            return redirect()->route('admin.users.edit-access', $this->userId);
        }

        session()->flash(
            'error', 
            'Failed to copy permissions. No permissions found or target user not found.');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.admin.users.copy-user-permissions-modal');
    }

    public static function closeModalOnEscape(): bool
    {
        return false;
    }

    public static function closeModalOnClickAway(): bool
    {
        return false;
    }

    public static function closeModalOnEscapeIsForceful(): bool
    {
        return false;
    }

    public static function destroyOnClose(): bool
    {
        return false;
    }

    public static function dispatchCloseEvent(): bool
    {
        return true;
    }

    public static function modalMaxWidth(): string
    {
        return '2xl'; // Default to a reasonable size
    }

    public static function modalMaxWidthClass(): string
    {
        return 'max-w-2xl'; // Tailwind CSS class for max-width
    }
}
