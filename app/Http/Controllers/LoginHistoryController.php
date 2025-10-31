<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ClientHints;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginHistoryController extends Controller
{
    use ClientHints;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $loginHistories = LoginHistory::with('user')
            ->latest()
            ->paginate(10);
        return view('admin.login_histories.index', compact('loginHistories'));
    }

    /**
     * Display the specified resource.
     */
    // public function show(LoginHistory $loginHistory): View
    // {
    //     $loginHistory->load('user');
    //     return view('admin.login_histories.show', compact('loginHistory'));
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoginHistory $loginHistory): RedirectResponse
    {
        $loginHistory->delete();

        return redirect()->route('admin.login-histories.index')
            ->with('success', 'Login history deleted successfully.');
    }

    /**
     * Parse browser information from User-Agent string
     */
    private function parseUserAgent(string $userAgent): string
    {
        $browserPatterns = [
            'Chrome'  => '/Chrome\/([0-9.]+)/',
            'Firefox' => '/Firefox\/([0-9.]+)/',
            'Safari'  => '/Safari\/([0-9.]+)/',
            'Edge'    => '/Edg\/([0-9.]+)/',
            'Opera'   => '/OPR\/([0-9.]+)/',
        ];

        foreach ($browserPatterns as $browser => $pattern) {
            if (preg_match($pattern, $userAgent, $matches)) {
                return "$browser $matches[1]";
            }
        }

        return 'Unknown Browser';
    }

    /**
     * Detect platform from User-Agent string
     */
    private function detectPlatform(string $userAgent): string
    {
        $platforms = [
            'Windows' => '/Windows NT ([0-9.]+)/',
            'Mac'     => '/Macintosh/',
            'iOS'     => '/iPhone|iPad|iPod/',
            'Android' => '/Android ([0-9.]+)/',
            'Linux'   => '/Linux/',
        ];

        foreach ($platforms as $platform => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $platform;
            }
        }

        return 'Unknown Platform';
    }
}
