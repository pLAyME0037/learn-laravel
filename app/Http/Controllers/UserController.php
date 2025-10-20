<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.view')->only('index', 'show');
        $this->middleware('permission:users.create')->only('create', 'store');
        $this->middleware('permission:users.edit')->only('edit', 'update');
        $this->middleware('permission:users.delete')->only('destroy');
        $this->middleware('has_permission:users.delete')->only('restore', 'forceDelete');
        $this->middleware('has_permission:users.edit')->only('updateStatus');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $selectedRole = $request->get('role');
        $status = $request->get('status');

        $users = User::withTrashed()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($selectedRole, function ($query, $selectedRole) {
                return $query->whereHas('role', function ($q) use ($selectedRole) {
                    $q->where('name', $selectedRole);
                });
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
            ->latest()
            ->paginate(15)
            ->withQueryString()
        ;
        $roles = Role::all();
        return view('users.index', compact('users', 'roles', 'search', 'selectedRole', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
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
            'role'     => 'required|in:super_user,admin,hod,register,staff,user,student',
            'bio'      => 'nullable|string|max:500',
        ]);

        $role = Role::where('name', $validated['role'])->firstOrFail();

        User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'username'          => $validated['username'],
            'password'          => Hash::make($validated['password']),
            'role_id'           => $role->getKey(), // Use getKey() for primary key
            'bio'               => $validated['bio'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User create successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $roles = Role::all();
        $user->load('role');
        return view('users.show', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
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
            'role'      => 'required|in:super_user,admin,hod,register,staff,user,student',
            'bio'       => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $role                 = Role::where('name', $validated['role'])->firstOrFail();
        $validated['role_id'] = $role->getKey(); // Use getKey() for primary key
        unset($validated['role']);

        $user->update($validated);
        return redirect()->route('admin.users.index')
            ->with('success', 'User Update Successfully');
    }

    private function checkSelfModification(User $user, string $key, string $value)
    {
        // Prevent admin from deleting themselves
        if (auth()->check() && $user->getKey() === auth()->user()->id) {
            return redirect()->route('admin.users.index')
                ->with(
                    $key,
                    $value
                );
        }
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
