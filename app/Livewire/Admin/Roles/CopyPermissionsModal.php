<?php

namespace App\Livewire\Admin\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class CopyPermissionsModal extends Component
{
    public $roleId;
    public $sourceRoleId;
    public $roles;

    public function mount($roleId)
    {
        $this->roleId = $roleId;
        $this->roles = Role::where('id', '!=', $roleId)->get();
    }

    public function copyPermissions()
    {
        $targetRole = Role::findById($this->roleId);
        $sourceRole = Role::findById($this->sourceRoleId);

        if ($targetRole && $sourceRole) {
            $targetRole->syncPermissions($sourceRole->permissions);
            session()->flash('success', 'Permissions copied successfully!');
            $this->dispatch('closeModal');
            return redirect()->route('admin.roles.edit-permissions', $this->roleId);
        }

        session()->flash('error', 'Failed to copy permissions.');
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.admin.roles.copy-permissions-modal');
    }
}
