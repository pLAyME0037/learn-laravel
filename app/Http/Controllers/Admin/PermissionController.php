<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PermissionController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Permission::class, 'permission');
    }

    /**
     * Logic move to Livewire : PermissionIndex
     */
    public function index(): View {
        return view('admin.permissions.index');
    }

    public function create(): View {
        $roles = Role::orderBy('name')->get();
        return view('admin.permissions.create', compact('roles'));
    }

    public function store(StorePermissionRequest $request): RedirectResponse {
        $validate = $request->validated();

        /**
         * @var Permission $permission
         */
        $permission = Permission::create([
            'name' => $validate['name'],
            'group' => $validate['group'],
            'guard_name' => $validate['guard_name'],
            'description' => $validate['description'] ?? null,
        ]);

        // Assign the role to the permission if role_id is present
        if (! isset($validate['role_id'])) {
            $role = Role::findById((int) $validate['role_id']);
            $permission->assignRole($role);
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');

    }

    public function edit(Permission $permission): View {
        $roles = Role::orderBy('name')->get();
        $currentRoleId = $permission->roles->first()?->id;
        return view('admin.permissions.edit', compact(
            'permission',
            'roles',
            'currentRoleId'
        ));
    }

    public function update(
        UpdatePermissionRequest $request,
        Permission $permission
    ): RedirectResponse {
        $validate = $request->validated();

        $permission->update([
            'name' => $validate['name'],
            'group' => $validate['group'],
            'guard_name' => $validate['guard_name'],
            'description' => $validate['description'],
        ]);

        if (! empty($validate['role_id'])) {
            $role = Role::findById((int) $validate['role_id']);
            $permission->syncRoles([$role]);
        } else {
            $permission->syncRoles([]);
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    // public function destroy(Permission $permission): RedirectResponse {
    //     $permission->delete();
    //     return redirect()->route('admin.permissions.index')
    //         ->with('success', 'Course deleted successfully.');
    // }
}
