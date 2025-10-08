<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
    // Debug: Check what data is coming through
    logger('Request data:', $request->all());
    logger('Validated data:', $request->validated());

        $user->fill($request->validated());

    // Debug: Check what's being filled
    logger('User data after fill:', $user->toArray());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            if (
                $user->profile_pic &&
                ! filter_var($user->profile_pic, FILTER_VALIDATE_URL)
            ) {
                Storage::disk('public')->delete($user->profile_pic);
            }

            $user->profile_pic = $request
                ->file('profile_pic')
                ->store('profile-pictures', 'public');
        }

        // Handle profile picture removal
        if (
            $request->has('remove_profile_pic') &&
            $request->boolean('remove_profile_pic')
        ) {
            if (
                $user->profile_pic &&
                ! filter_var($user->profile_pic, FILTER_VALIDATE_URL)
            ) {
                Storage::disk('public')->delete($user->profile_pic);
            }
            $user->profile_pic = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if (
            $user->profile_pic &&
            ! filter_var($user->profile_pic, FILTER_VALIDATE_URL)
        ) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
