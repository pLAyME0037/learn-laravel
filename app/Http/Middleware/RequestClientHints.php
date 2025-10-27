<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestClientHints
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Request Client Hints headers
        $response->headers->set(
            'Accept-CH',
            'Sec-CH-UA,
            Sec-CH-UA-Mobile,
            Sec-CH-UA-Platform,
            Sec-CH-UA-Platform-Version'
        );

        // Allow these headers for a day (86400 seconds)
        $response->headers->set(
            'Critical-CH',
            'Sec-CH-UA, Sec-CH-UA-Platform'
        );
        $response->headers->set('Accept-CH-Lifetime', '86400');

        return $response;
    }
}
