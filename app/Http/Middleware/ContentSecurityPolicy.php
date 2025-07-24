<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next)
    {
        $rules = "default-src 'none'; script-src 'none'; style-src 'none'; img-src 'none'; connect-src 'self'; frame-ancestors 'none';";

        $response = $next($request);

        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->header('Cross-Origin-Resource-Policy', 'same-origin');
        $response->header('Content-Security-Policy', $rules);
        $response->header('X-Content-Security-Policy', $rules);
        $response->header('X-WebKit-CSP', $rules);
        $response->header('Feature-Policy', "camera 'none'; microphone 'none';");
        $response->header('Set-Cookie', 'Secure');
        $response->header('X-Frame-Options', 'DENY');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('X-Permitted-Cross-Domain-Policies', 'none');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->header('Referrer-Policy', 'no-referrer');
        //$response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
