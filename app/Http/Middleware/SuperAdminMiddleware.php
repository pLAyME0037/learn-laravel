<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isSuperUser()){
            if ($request->acceptsJson()) {
                return response()->json([
                    'error' => 'Unauthorized access.'
                ], 403);
            }
            return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to acccess this Page.');
        }
        return $next($request);
    }
}
