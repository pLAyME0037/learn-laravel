<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        Auth::logoutOtherDevices($request->input('password'));

        // Update last login timestamp
        $request->user()->update(['last_login_at' => now()]);

        // Record login history
        LoginHistory::create([
            'user_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now(),
        ]);

        return redirect()->intended(route(
            'dashboard', absolute: false)
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
