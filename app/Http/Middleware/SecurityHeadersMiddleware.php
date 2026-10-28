<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Chống Clickjacking (CWE-1021)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Chống MIME-sniffing (CWE-693)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Bảo vệ trình duyệt cũ khỏi XSS (Reflected XSS filter)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Giới hạn rò rỉ URL trong Referer (CWE-200)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Chính sách quyền thiết bị (Permissions Policy)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        // 6. Triệt tiêu hoàn toàn header X-Powered-By nếu còn sót lại (CWE-497 / CWE-1395)
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
            @header_remove('Server');
        }
        $response->headers->remove('X-Powered-By');

        // 7. HSTS nếu kết nối là HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // 8. Content-Security-Policy (CSP) (CWE-693)
        // Cung cấp chính sách tương thích với các thư viện frontend hiện có (Google Fonts, Tailwind, CDN, Leaflet, Chart.js)
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https: ws: wss: http://localhost:* http://127.0.0.1:*",
            "frame-ancestors 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
