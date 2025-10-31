<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.view')
            ->only('index', 'show');
        $this->middleware('permission:users.create')
            ->only('create', 'store');
        $this->middleware('permission:users.edit')
            ->only('edit', 'update');
        $this->middleware('permission:users.delete')
            ->only('destroy');
        $this->middleware('has_permission:users.delete')
            ->only('restore', 'forceDelete');
        $this->middleware('has_permission:users.edit')
            ->only('updateStatus');                                                         
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search       = $request->get('search');
        $selectedRole = $request->get('role');
        $status       = $request->get('status');

        $users = User::withTrashed()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($selectedRole !== null && $selectedRole !== '', function ($query) use ($selectedRole) {
                if ($selectedRole === 'no_roles') {
                    // Filter for users with no roles
                    return $query->doesntHave('roles');
                } else {
                    // Filter for users with a specific role
                    return $query->whereHas('roles', function ($q) use ($selectedRole) {
                        $q->where('name', $selectedRole);
                    });
                }
            })
            ->when($status === 'active', function ($query) {
                return $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                return $query->where('is_active', false);
            })
            ->when($status === 'trashed', function ($query) {
                return $query->onlyTrashed();
            })
            ->with('roles') // Eager load Spatie roles
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString()
        ;
        $roles = Role::orderBy('name', 'asc')->get();
        return view('admin.users.index', compact('users', 'roles', 'search', 'selectedRole', 'status'));
    }

    public function editAccess(User $user)
    {
        $allRoles = Role::orderBy('name', 'asc')->get();
        $userRoles = $user->roles->pluck('id')->toArray();

        $allPermissions = Permission::orderBy('name', 'asc')->get()->groupBy('group_name'); // Sort permissions alphabetically
        $userPermissions = $user->permissions->pluck('name')->toArray();

        return view('admin.users.edit-access', compact('user', 'allRoles', 'userRoles', 'allPermissions', 'userPermissions'));
    }

    public function updateAccess(Request $request, User $user)
    {
        $request->validate([
            'roles'         => 'array',
            'roles.*'       => 'exists:roles,id',
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $roles = Role::whereIn('id', $request->roles)->get();
        $user->syncRoles($roles);
        $user->syncPermissions($request->permissions);

        return redirect()->route('admin.users.index')->with('success', 'User roles and permissions updated successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|alpha_dash|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|exists:roles,name', // Validate role name against Spatie roles
            'bio'      => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'username'          => $validated['username'],
            'password'          => Hash::make($validated['password']),
            'bio'               => $validated['bio'],
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']); // Assign role using Spatie

        return redirect()->route('admin.users.index')
            ->with('success', 'User create successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles'); // Use the correct 'roles' relationship for Spatie
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles         = Role::all();
        $userRoleNames = $user->getRoleNames()->toArray(); // Get Spatie role names
        return view('admin.users.edit', compact('user', 'roles', 'userRoleNames'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|alpha_dash|unique:users,username,' . $user->getKey(),
            'email'     => 'required|string|email|max:255|unique:users,email,' . $user->getKey(),
            'role'      => 'required|string|exists:roles,name', // Validate role name exists
            'bio'       => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
        ]);

        // Sync the role from the edit form. This will replace all existing roles.
        $user->syncRoles([$validated['role']]);

        // Prepare data for user update, excluding the role
        $updateData = $request->only('name', 'username', 'email', 'bio');
        $updateData['is_active'] = $request->has('is_active');

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    private function checkSelfModification(User $user, string $key, string $value)
    {
        // Prevent admin from modifying themselves
        if (Auth::check() && $user->getKey() === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with(
                    $key,
                    $value
                );
        }
        return null; // Return null if no redirection is needed
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->checkSelfModification(
            $user,
            'success',
            'User deleted successfully.'
        );
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function restore($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);
        $user->restore();

        return redirect()->route('admin.users.index')
            ->with('success', 'User restore successfully.');
    }

    public function forceDelete($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);
        $this->checkSelfModification(
            $user,
            'error',
            'You cannot permanently delete your own account.'
        );
        $user->forceDelete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User permanently deleted.');
    }

    public function updateStatus(User $user)
    {
        $this->checkSelfModification(
            $user,
            'error',
            'You cannot deactivate your own account.'
        );

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.users.index')
            ->with('success', "User {$status} successfully");
    }
}
