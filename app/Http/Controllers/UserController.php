<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate filters
        $filters = $request->validate([
            'search'  => 'nullable|string|max:100',
            'role'    => 'nullable|string|max:50',
            'status'  => 'nullable|string|in:active,inactive,trashed',
            'orderby' => 'nullable|string|in:newest,oldest,a_to_z,z_to_a',
        ]);

        $users = User::with('roles')
            ->applyFilters($filters)
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->pluck('name', 'name');

        return view('admin.users.index', [
            'users'   => $users,
            'roles'   => $roles,
            'filters' => $filters,
        ]);
    }

    public function editAccess(User $user)
    {
        $allRoles  = Role::orderBy('name', 'asc')->get();
        $userRoles = $user->roles->pluck('id')->toArray();

        $allPermissions  = Permission::orderBy('name', 'asc')->get()->groupBy('group');
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        return view(
            'admin.users.edit-access',
            compact(
                'user',
                'allRoles',
                'userRoles',
                'allPermissions',
                'userPermissions'
            ));
    }

    public function updateAccess(Request $request, User $user)
    {
        $request->validate([
            'roles'         => 'array',
            'roles.*'       => 'exists:roles,id',
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $roles = $request->roles ? Role::whereIn('id', $request->roles)->get() : collect();
        $user->syncRoles($roles);
        $user->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', 'User roles and permissions updated successfully.');
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
            'role'     => 'required|string|exists:roles,name',
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
        $roles = Role::orderBy('name')->pluck('name', 'name');
        $canChangePassword = Auth::user()->can('changePassword', $user);
        return view('admin.users.edit', compact('user', 'roles', 'canChangePassword'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        DB::beginTransaction();

        try {
            $this->authorize('changePassword', $user);

            $validated = $request->validate([
                'name'      => 'required|string|max:255',
                'username'  => 'required|string|max:255|alpha_dash|unique:users,username,' . $user->id,
                'email'     => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'bio'       => 'nullable|string|max:500',
                'is_active' => 'sometimes|boolean',
                'password'  => ['nullable', 'confirmed', Password::min(8)],
            ]);

            DB::transaction(function () use ($user, $validated) {
                $user->update([
                    'name'      => $validated['name'],
                    'username'  => $validated['username'],
                    'email'     => $validated['email'],
                    'bio'       => $validated['bio'],
                    'is_active' => $validated['is_active'] ?? $user->is_active,
                ]);

                if (! empty($validated['password'])) {
                    $user->update(['password' => Hash::make($validated['password'])]);
                }
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update failed: ' . $e->getMessage(), ['user_id' => $user->id]);
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    private function checkSelfModification(User $user, string $key, string $value)
    {
        // Prevent admin from modifying themselves
        if (Auth::check() && $user->getKey() === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with($key, $value);
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

    public function restore(User $user)
    {
        $this->authorize('restore', $user);
        $user->restore();

        return redirect()->route('admin.users.index')
            ->with('success', 'User restore successfully.');
    }

    public function forceDelete(User $user)
    {
        $this->authorize('forceDelete', $user);
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

        $this->authorize('updateStatus', $user);
        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.users.index')
            ->with('success', "User {$status} successfully");
    }
}
