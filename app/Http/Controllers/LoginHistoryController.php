<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ClientHints;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginHistoryController extends Controller
{
    use ClientHints;
    public function index(Request $request, Response $response)
    {
        $loginHistories = LoginHistory::with('user')
            ->latest()
            ->paginate(15);
        return view('login-history.index', compact('loginHistories'));
    }

    /**
     * Store a new login history record.
     * This method would typically be called after a successful user login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Response $response)
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get client IP address
        $ipAddress = $request->ip();

        // Capture raw user agent and use the ClientHints trait to extract hints with fallbacks
        $userAgent       = $request->userAgent();
        $hints           = $this->getClientHints($request);
        $secChUa         = $hints['sec_ch_ua'];
        $secChUaPlatform = $hints['sec_ch_ua_platform'];

        // Create a new LoginHistory record
        $loginHistory = LoginHistory::create([
            'user_id'            => $userId,
            'ip_address'         => $ipAddress,
            'Sec_Ch_Ua'          => $secChUa,
            'Sec_Ch_Ua_Platform' => $secChUaPlatform,
            'user_agent'         => $userAgent,
            'login_at'           => now(),
        ]);

        return $response->json([
            'message' => 'Login history recorded successfully',
            'data'    => $loginHistory,
        ], 201);
    }

    /**
     * Display a specific login history record by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Response $response)
    {
        $history = LoginHistory::find($id);

        if (! $history) {
            return $response->json(['message' => 'Login history not found'], 404);
        }

        // Accessing specific attributes
        $secChUa         = $history->Sec_Ch_Ua;
        $secChUaPlatform = $history->Sec_Ch_Ua_Platform;
        $userAgent       = $history->user_agent;

        return $response->json([
            'user_id'            => $history->user_id,
            'ip_address'         => $history->ip_address,
            'Sec_Ch_Ua'          => $secChUa,
            'Sec_Ch_Ua_Platform' => $secChUaPlatform,
            'user_agent'         => $userAgent,
            'login_at'           => $history->login_at,
        ]);
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
