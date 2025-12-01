<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $search      = $request->get('search');
        $permissions = Permission::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('group_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->with('roles')
            ->orderBy('name', 'asc')
            ->paginate(20);

        return view('admin.permissions.index', compact('permissions', 'search'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.permissions.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Set default for 'group' if not provided
        if (! $request->has('group')) {
            $request->merge(['group' => 'web']);
        }

        $validate = $request->validate([
            'name'        => 'required|string|max:255',
            'group'       => 'required|string',
            'group_name'  => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'role_id'     => 'required|string|exists:roles,id',
        ]);

        $permission = Permission::create($validate);

        // Assign the role to the permission if role_id is present
        if (isset($validate['role_id'])) {
            $role = Role::findById($validate['role_id']);
            if ($role) {
                $permission->assignRole($role);
            }
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');

    }

    public function edit(Permission $permission)
    {
        $roles = Role::all();
        return view('admin.permissions.edit', compact('permission', 'roles'));
    }

    public function update(Request $request, Permission $permission)
    {
        // Set default for 'group' if not provided
        if (! $request->has('group')) {
            $request->merge(['group' => 'web']);
        }

        $validate = $request->validate([
            'name'        => 'required|string|max:255',
            'group'       => 'required|string',
            'group_name'  => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'role_id'     => 'required|string|exists:roles,id',
        ]);

        $permission->update($validate);

        // Sync the role to the permission if role_id is present
        if (isset($validate['role_id'])) {
            $role = Role::findById($validate['role_id']);
            if ($role) {
                $permission->syncRoles([$role]);
            }
        } else {
            // If no role_id is provided, detach all roles
            $permission->syncRoles([]);
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Course deleted successfully.');
    }
}
