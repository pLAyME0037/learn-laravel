<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $role = '';
    public $status = '';

    protected $queryString = ['search', 'role', 'status'];

    public function render()
    {
        $users = User::withTrashed()
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($this->role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->when($this->status === 'inactive', function ($query) {
                return $query->where('is_active', false);
            })
            ->when($this->status === 'trashed', function ($query) {
                return $query->onlyTrashed();
            })
            ->latest()
            ->paginate(15);

        return view('livewire.user-index', [
            'users' => $users,
        ]);
    }
}
