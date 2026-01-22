<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{
    public function __construct() {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
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

    public function editAccess(User $user) {
        $allRoles  = Role::orderBy('name', 'asc')->get();
        $userRoles = $user->roles->pluck('id')->toArray();

        $allPermissions  = Permission::orderBy('name', 'asc')->get()->groupBy('group');
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        return view( 'admin.users.edit-access', compact(
            'user',
            'allRoles',
            'userRoles',
            'allPermissions',
            'userPermissions'
        ));
    }

    public function updateAccess(Request $request, User $user) {
        $request->validate([
            'roles'         => 'array',
            'roles.*'       => 'exists:roles,id',
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($user->id === Auth::id()) {
            abort(403, "Cannot change your own access.");
        }

        DB::transaction(function() use ($request, $user) {
            $roles = $request->roles
                ? Role::whereIn('id', $request->roles)->get()
                : collect();
            $user->syncRoles($roles);
            $user->syncPermissions($request->permissions ?? []);
        });

        return redirect()->route('admin.users.index')
                         ->with('success', 'User roles and permissions updated successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request) {
        $data = $request->validated();

        if ($data['role'] === "Super Administrator"
            && ! Auth::user()->hasRole("Super Administrator")
        ) abort(403, "Cannot create user with higher privileges than yourself.");

        try {
            DB::transaction(function() use ($data) {
                $user = User::create([
                    'name'              => $data['name'],
                    'email'             => $data['email'],
                    'username'          => $data['username'],
                    'password'          => Hash::make($data['password']),
                    'bio'               => $data['bio'],
                    'email_verified_at' => now(),
                ]);

                $user->assignRole($data['role']); // Assign role using Spatie
            });
            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create user: ' . $e->getMessage());
            return redirect()->back()
                 ->with('error', 'Failed to create user: ' . $e->getMessage())
                 ->withInput();
        }

        return redirect()->route('admin.users.index')
             ->with('success', 'User create successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user) {
        $user->load('roles'); // Use the correct 'roles' relationship for Spatie
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user) {
        $roles = Role::orderBy('name')->pluck('name', 'name');
        $canChangePassword = Auth::user()->can('changePassword', $user);
        return view('admin.users.edit', compact(
            'user',
            'roles',
            'canChangePassword'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse {
        $validated = $request->validated();
        $userData = [
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'bio'       => $validated['bio'],
        ];

        if (isset($validated["is_active"])) {
            $userData["is_active"] = $validated["is_active"];
        }

        if (empty($validated['password'])) { return $newHashPass = null; }
        else { $newHashPass = Hash::make($validated['password']); }

        try {
            DB::transaction(function () use ($user, $userData, $newHashPass) {
                $user->update($userData);

                if ($newHashPass) {
                    $user->update(['password' => $newHashPass]);
                }
            });

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error(
                'User update failed: ' . $e->getMessage(),
                ['user_id' => $user->id]
            );
            return redirect()->back()
                 ->with('error', 'Failed to update user: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')
             ->with('success', 'User updated successfully.');
    }

    private function checkSelfModif(
        User $user,
        string $key,
        string $value
    ) {
        // Prevent admin from modifying themselves
        if (Auth::check() && $user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with($key, $value);
        }
        return null;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) {
        $this->checkSelfModif($user,
            'success', 'User deleted successfully.'
        );
        $user->delete();

        return redirect()->route('admin.users.index')
             ->with('success', 'User deleted successfully.');
    }

    public function restore(User $user) {
        $this->authorize('restore', $user);
        $user->restore();

        return redirect()->route('admin.users.index')
             ->with('success', 'User restore successfully.');
    }

    public function forceDelete(string $id) {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $user);

        $this->checkSelfModif($user,
            'error', 'You cannot permanently delete your own account.'
        );
        $user->forceDelete();

        return redirect()->route('admin.users.index')
             ->with('success', 'User permanently deleted.');
    }

    public function updateStatus(User $user) {
        $this->authorize("updateStatus", $user);
        $this->checkSelfModif($user,
            "error", "You cannot deactivate your own account."
        );

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.users.index')
             ->with('success', "User {$status} successfully");
    }
}
