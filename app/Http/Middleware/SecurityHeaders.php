<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Menambahkan security headers ke setiap response aplikasi.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | Clickjacking Protection
        |--------------------------------------------------------------------------
        |
        | Halaman hanya boleh ditampilkan di dalam frame dari origin yang sama.
        |
        */
        $response->headers->set(
            'X-Frame-Options',
            'SAMEORIGIN'
        );

        /*
        |--------------------------------------------------------------------------
        | MIME Sniffing Protection
        |--------------------------------------------------------------------------
        |
        | Mencegah browser menebak Content-Type yang berbeda dari response.
        |
        */
        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        /*
        |--------------------------------------------------------------------------
        | Referrer Policy
        |--------------------------------------------------------------------------
        |
        | Membatasi informasi URL yang dikirim ketika pengguna berpindah
        | ke origin atau website lain.
        |
        */
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        /*
        |--------------------------------------------------------------------------
        | Browser Permissions
        |--------------------------------------------------------------------------
        |
        | Aplikasi Ragen Swim Course saat ini tidak membutuhkan akses
        | kamera, mikrofon, maupun lokasi pengguna.
        |
        */
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        return $response;
    }
}
