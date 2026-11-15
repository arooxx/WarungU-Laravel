<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan untuk semua respons.
 *
 * CSP memakai 'unsafe-inline' untuk script dan style karena halaman memakai
 * atribut onclick dan blok <script>/<style> di dalam view. Sumber eksternal
 * dibatasi hanya ke CDN yang memang dipakai (Bootstrap, Font Awesome, Chart.js, Google Fonts).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com",
            "img-src 'self' data:",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]);

        $h = $response->headers;
        $h->set('Content-Security-Policy', $csp);
        $h->set('X-Content-Type-Options', 'nosniff');
        $h->set('X-Frame-Options', 'DENY');
        $h->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $h->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $h->set('Cross-Origin-Opener-Policy', 'same-origin');

        // HSTS hanya dikirim lewat HTTPS (di Vercel permintaan sudah HTTPS dan proxy dipercaya)
        if ($request->isSecure()) {
            $h->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // X-Powered-By dikirim oleh PHP sendiri (expose_php), jadi dihapus di tingkat PHP
        header_remove('X-Powered-By');
        $h->remove('X-Powered-By');

        return $response;
    }
}
