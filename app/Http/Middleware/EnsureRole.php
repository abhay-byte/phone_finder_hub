<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Allows super_admin automatically, otherwise checks if user's role is in the allowed list.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            abort(403, 'This area is restricted.');
        }

        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'This area is restricted to authorized personnel.');
    }
}
