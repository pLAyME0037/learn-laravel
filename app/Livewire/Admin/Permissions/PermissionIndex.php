<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Permissions;

use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class PermissionIndex extends Component
{
    use WithPagination;

    #[Url(history:true)]
    public string $search = '';
    #[Url(history:true)]
    public string $groupFilter = '';
    #[Locked]
    public ?int $permIdToDel = null;

    /**
     * Trigger property when updated
     * @return void
     */
    public function updated($property): void {
        $filters = ['search', 'groupFilter'];
        if (in_array($property, $filters)) {
            $this->resetPage();
        }
    }

    /**
     * Get unique group for filter dropdown
     * @return array<string>
     */
    public function getGroupsProperty(): array {
        return Permission::query()
            ->select('group')       // 1. Select only the group column
            ->distinct()            // 2. SQL distrinct (no duplicate)
            ->whereNotNull('group') // 3. filter out empty group
            ->orderBy('group')      // 4. sort alphabically by group
            ->pluck('group')        // 5. get just the value no key
            ->toArray();
    }

    /**
     * func trigger by view
     * @return void
     */
    public function confDel(int $id): void {
        $this->permIdToDel = $id;
        $permission = Permission::findOrFail($id);

        $this->dispatch("swal:confirm", [
            'title' => 'Are you sure?',
            'text' => "You are about to delete {$permission->name}.",
            'icon' => 'warning',
            'method' => 'destroy',
            'confirmButtonText' => 'Yes, Delete',
            'confirmButtonColor' => '#dc2626',
        ]);
    }

    #[On('destroy')]
    public function destroy():void {
        Gate::authorize('Manage Roles & Permissions');

        if (! $this->permIdToDel) return;

        $perm = Permission::find($this->permIdToDel);
        if ($perm) {
            $perm->delete();
            $this->dispatch("swal:success", [
                'message' => 'Permission delete successfully.'
            ]);
        } else {
            $this->dispatch("swal:error", [
                'message' => 'Permission not found.'
            ]);
        }

        $this->permIdToDel = null;
    }

    public function render(): View {
        $permissions = Permission::query()
            ->with('roles')
            ->when($this->search, function($q) {
                $term = '%' . $this->search . '%';
                $q->where(fn($sq) =>
                    $sq->where('name', 'like', $term)
                       ->orWhere('description', 'like', $term)
                );
            })
            ->when($this->groupFilter, fn($q) =>
                $q->where('group', $this->groupFilter)
            )
            ->orderBy('group', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.admin.permissions.permission-index', [
            'permissions' => $permissions,
            'groupProperty' => $this->getGroupsProperty(),
        ]);
    }
}
