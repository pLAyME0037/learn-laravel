<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRoles = [];

    public function render() {
        $users = User::query()
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->with('roles')
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function assignRole($userId, $roleName) {
        $user = User::find($userId);
        if ($user) {
            $user->assignRole($roleName);
            session()->flash('message', 'Role assigned successfully.');
        }
    }

    public function removeRole($userId, $roleName) {
        $user = User::find($userId);
        if ($user) {
            $user->removeRole($roleName);
            session()->flash('message', 'Role removed successfully.');
        }
    }
}
