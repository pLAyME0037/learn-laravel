<?php

declare (strict_types = 1);

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait HandlesAccessDenial
{
    /**
     * Standardized Forbidden Response.
     */
    protected function denyAccess(
        Request $request,
        string $message = 'Unauthorized access.'
    ): Response {
        if ($request->acceptsJson()) {
            return response()->json(['error' => $message], 403);
        }

        return redirect()
            ->route('dashboard')
            ->with('error', $message);
    }
}
