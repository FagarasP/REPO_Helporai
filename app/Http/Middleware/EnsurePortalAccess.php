<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalAccess
{
    public function handle(Request $request, Closure $next, string $portal): Response
    {
        $user = $request->user();

        if (! $user || ! $user->canAccessPortal($portal)) {
            abort(403);
        }

        return $next($request);
    }
}
