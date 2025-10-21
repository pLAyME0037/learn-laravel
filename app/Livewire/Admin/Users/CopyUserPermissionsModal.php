<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CopyUserPermissionsModal extends Component
{
    public $userId;
    public $sourceType = 'role'; // 'role' or 'user'
    public $sourceId;
    public $users;
    public $roles;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->users = User::where('id', '!=', $userId)->get();
        $this->roles = Role::all();
    }

    public function copyPermissions()
    {
        $targetUser = User::find($this->userId);
        $permissionsToCopy = collect();

        if ($this->sourceType === 'role') {
            $sourceRole = Role::findById($this->sourceId);
            if ($sourceRole) {
                $permissionsToCopy = $sourceRole->permissions->pluck('name');
            }
        } elseif ($this->sourceType === 'user') {
            $sourceUser = User::find($this->sourceId);
            if ($sourceUser) {
                $permissionsToCopy = $sourceUser->permissions->pluck('name');
            }
        }

        if ($targetUser && $permissionsToCopy->isNotEmpty()) {
            $targetUser->syncPermissions($permissionsToCopy->toArray());
            session()->flash('success', 'Permissions copied successfully!');
            $this->dispatch('closeModal');
            return redirect()->route('admin.users.edit-access', $this->userId);
        }

        session()->flash('error', 'Failed to copy permissions.');
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.admin.users.copy-user-permissions-modal');
    }
}
