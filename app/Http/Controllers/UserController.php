<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
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
            'role'     => 'required|in:admin,staff,user',
            'bio'      => 'nullable|string|max:500',
        ]);

        User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'username'          => $validated['username'],
            'password'          => Hash::make($validated['email']),
            'role'              => $validated['role'],
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
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required
                |string
                |email
                |max:255
                |unique:users,email,' . $user->id,
            'username'  => 'required
                |string
                |max:255
                |alpha_dash
                |unique:users,username,' . $user->id,
            'role'      => 'required|in:admin,staff,user',
            'bio'       => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $user->update($validated);
        return redirect()->route('admin.users.index')
            ->with('success', 'User Update Successfully');
    }

    private function delCheckAdmin(User $user, string $key, string $value)
    {
        // Prevent admin form deleting themselves
        if ($user->id === auth()->id()) {
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
        $this->delCheckAdmin(
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
        $this->delCheckAdmin(
            $user,
            'error',
            'You cannot permanently delete your own account.'
        );
        $user->forceDelete();
        return redirect()->route('admin.users.index')
        ->with('success', 'User permanently deleted.');
    }

    public function updateStatus(User $user) {
        $this->delCheckAdmin(
            $user,
            'error',
            'You cannot deactivate your own account.'
        );

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.users.index')
        ->with('success', "User {$status} successfully");
    }
}
