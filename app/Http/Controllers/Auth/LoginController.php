<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * LoginController
 *
 * Handles authentication for internal staff only.
 * Customers/guests do NOT log in — they order directly as guests.
 *
 * Login is via `username` (not email) since staff have dedicated usernames.
 *
 * Security measures:
 *  - Session regeneration on successful login (prevents session fixation)
 *  - Session invalidation on logout
 *  - Generic error message (does not reveal whether username or password is wrong)
 *  - Account expiry check after authentication (fail-closed)
 *  - Rate limiting applied in routes (throttle:10,1)
 *
 * TODO(security): MFA not implemented — consider TOTP for Administrator accounts.
 * TODO(security): OAuth/SSO provider not implemented.
 */
class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'max:128'],
        ]);

        // Attempt login with `username` field
        if (! Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            // Generic error — do NOT reveal whether username or password is wrong
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau password tidak sesuai.']);
        }

        $user = Auth::user();

        // ── Account expiry check (fail-closed) ───────────────────────────────
        if ($user->isExpired()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                             ->withErrors(['username' => 'Akun Anda sudah kadaluarsa. Hubungi Administrator.']);
        }

        // ── Session fixation prevention ──────────────────────────────────────
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        // Invalidate & regenerate session token on logout
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
