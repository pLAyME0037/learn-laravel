<?php
namespace App\Http\Middleware;

use App\Http\Traits\HandlesAccessDenial;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InstructorMiddleware
{
    use HandlesAccessDenial;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Guard: Check Authentication
        if (! Auth::check()) {
            return $this->denyAccess($request);
        }

        // 2. Guard: Check Authorization
        if (! Auth::user()->isStudent()) {
            return $this->denyAccess($request,  'This page preserve only for student.');
        }

        return $next($request);
    }
}
