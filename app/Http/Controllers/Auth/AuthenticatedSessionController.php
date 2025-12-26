<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\ClientHints;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    use ClientHints;
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

        // Record login history (include Client Hints when available)
        $hints = $this->getClientHints($request);

        LoginHistory::create([
            'user_id'            => $request->user()->id,
            'ip_address'         => $request->ip(),
            'Sec_Ch_Ua'          => $hints['sec_ch_ua'],
            'Sec_Ch_Ua_Platform' => $hints['sec_ch_ua_platform'],
            'user_agent'         => $request->userAgent(),
            'login_at'           => now(),
        ]);

        $user = $request->user();
        $level1 = $user->hasAnyRole(['Super Administrator', 'admin']);
        $level2 = $user->hasAnyRole(['instructor', 'profressor', 'staff']);
        $level3 = $user->hasRole('student');

        return match(true) {
            $level1 => redirect()->intended(route('admin.dashboard')),
            $level2 => redirect()->intended(route('instructor.dashboard')),
            $level3 => redirect()->intended(route('academic.dashboard')),
            default => redirect()->intended(route('dashboard')),
        };
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
