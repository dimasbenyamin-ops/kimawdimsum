<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-based access control middleware.
 * Usage in routes: ->middleware('role:admin') or ->middleware('role:cashier,admin')
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Must be authenticated first (auth middleware handles the redirect)
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Fail closed: deny if user role not in allowed list
        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Akses tidak diizinkan untuk peran Anda.');
        }

        return $next($request);
    }
}
