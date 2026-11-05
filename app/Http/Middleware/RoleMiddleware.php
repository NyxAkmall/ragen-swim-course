<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Memeriksa apakah user yang sedang login
     * mempunyai role yang diperbolehkan
     * untuk mengakses halaman tertentu.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        /**
         * Jika belum login, arahkan ke login.
         */
        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'auth' => 'Silakan login terlebih dahulu.',
                ]);
        }

        $user = Auth::user();

        /**
         * User dengan akun tidak aktif
         * tidak diperbolehkan menggunakan sistem.
         */
        if (!$user->isActive()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'auth' => 'Akun Anda sedang tidak aktif. Silakan hubungi admin Ragen Swim Course.',
                ]);
        }

        /**
         * Cek apakah role user termasuk
         * dalam role yang diperbolehkan.
         */
        if (!in_array($user->role, $roles, true)) {
            abort(
                403,
                'Anda tidak memiliki akses ke halaman ini.'
            );
        }

        return $next($request);
    }
}
