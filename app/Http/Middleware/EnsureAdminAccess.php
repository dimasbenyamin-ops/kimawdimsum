<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureAdminAccess Middleware
 *
 * Guards all backend (/admin) routes. Performs three checks in order:
 *
 *  1. Authentication — user must be logged in (fail-close: redirect to login)
 *  2. Expiry         — user's `expired_at` must not be past (fail-close: force logout)
 *  3. Permission     — user's role must have the requested route in `role_menu` (fail-close: 403)
 *
 * Usage in routes:
 *   Route::middleware('admin.access')->group(...)
 *
 * The permission check is dynamic: no hardcoded role names. All access is
 * driven by the `role_menu` pivot table managed by the Administrator.
 */
class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // ── 1. Authentication ────────────────────────────────────────────────
        if (! Auth::check()) {
            return redirect()->route('login')
                             ->with('error', 'Silakan login untuk melanjutkan.');
        }

        $user = Auth::user();

        // ── 2. Account Expiry ─────────────────────────────────────────────────
        if ($user->isExpired()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                             ->with('error', 'Akun Anda sudah kadaluarsa. Hubungi Administrator.');
        }

        // ── 3. Role-Menu Permission (dynamic, DB-driven) ──────────────────────
        $routeName = $request->route()?->getName() ?? '';

        // Allow top-level admin dashboard without a nav_menu entry
        // so that the dashboard itself doesn't need to be in nav_menus
        $openRoutes = ['admin.dashboard'];
        if (in_array($routeName, $openRoutes, true)) {
            return $next($request);
        }

        if (! $user->hasNavMenuAccess($routeName)) {
            abort(403, 'Akses ditolak: peran Anda tidak memiliki izin untuk menu ini.');
        }

        return $next($request);
    }
}
