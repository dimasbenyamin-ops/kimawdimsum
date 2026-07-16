<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Appends security headers to every HTTP response.
 *
 * TODO(security): Replace 'unsafe-inline' in CSP script-src with nonces once
 * Blade nonce support is wired (requires Laravel 11 CSP nonce helpers).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // In local/dev, allow all WebSocket origins (ngrok, Cloudflare tunnels, LAN IPs, etc.)
        $isLocal = app()->environment('local');
        $wsSrc   = $isLocal
            ? "ws: wss:"
            : "ws://localhost:8080 wss://localhost:8080";

        // Content-Security-Policy
        // TODO(security): Tighten script-src by using CSP nonces for inline scripts
        $csp = implode(' ', [
            "default-src 'self';",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://app.sandbox.midtrans.com https://app.midtrans.com https://static.cloudflareinsights.com;",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net;",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net;",
            "img-src 'self' data: blob:;",
            "connect-src 'self' {$wsSrc} https://cdn.jsdelivr.net https://app.sandbox.midtrans.com https://app.midtrans.com https://cloudflareinsights.com;",
            "frame-src 'self' https://app.sandbox.midtrans.com https://app.midtrans.com;",
            "object-src 'none';",
            "base-uri 'self';",
            "frame-ancestors 'none';",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
