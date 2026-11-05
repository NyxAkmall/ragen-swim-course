<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Maksimal percobaan login gagal.
     */
    private const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Lama blokir login dalam detik.
     */
    private const LOGIN_DECAY_SECONDS = 60;

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Normalisasi Email
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'email' => strtolower(
                trim((string) $request->input('email', ''))
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi Login
        |--------------------------------------------------------------------------
        */

        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ], [
            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'password.required' =>
                'Password wajib diisi.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Login Rate Limiting
        |--------------------------------------------------------------------------
        |
        | Rate limit dibuat berdasarkan kombinasi email dan IP.
        | Maksimal 5 percobaan login gagal dalam 60 detik.
        |
        */

        $throttleKey = $this->loginThrottleKey(
            $credentials['email'],
            $request
        );

        if (
            RateLimiter::tooManyAttempts(
                $throttleKey,
                self::MAX_LOGIN_ATTEMPTS
            )
        ) {
            $seconds = RateLimiter::availableIn(
                $throttleKey
            );

            return back()
                ->withInput(
                    $request->only('email')
                )
                ->withErrors([
                    'email' =>
                        'Terlalu banyak percobaan login. Silakan coba lagi dalam '
                        . $seconds
                        . ' detik.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Proses Login
        |--------------------------------------------------------------------------
        */

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit(
                $throttleKey,
                self::LOGIN_DECAY_SECONDS
            );

            return back()
                ->withInput(
                    $request->only('email')
                )
                ->withErrors([
                    'email' =>
                        'Email atau password yang Anda masukkan salah.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Login Berhasil
        |--------------------------------------------------------------------------
        */

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Cek Status Akun
        |--------------------------------------------------------------------------
        */

        if (!Auth::user()->isActive()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' =>
                        'Akun tidak aktif. Silakan hubungi admin Ragen Swim Course.',
                ]);
        }

        return $this->redirectByRole();
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Normalisasi Email
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'email' => strtolower(
                trim((string) $request->input('email', ''))
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi Registrasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ], [
            'name.required' =>
                'Nama lengkap wajib diisi.',

            'name.max' =>
                'Nama maksimal 100 karakter.',

            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.unique' =>
                'Email tersebut sudah terdaftar.',

            'phone.required' =>
                'Nomor WhatsApp wajib diisi.',

            'phone.max' =>
                'Nomor WhatsApp maksimal 20 karakter.',

            'phone.unique' =>
                'Nomor WhatsApp tersebut sudah terdaftar.',

            'password.required' =>
                'Password wajib diisi.',

            'password.confirmed' =>
                'Konfirmasi password tidak sama.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Buat Customer
        |--------------------------------------------------------------------------
        |
        | Role dan status tidak diambil dari request.
        | User yang mendaftar melalui halaman publik selalu menjadi customer.
        |
        */

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make(
                $validated['password']
            ),
            'role' => 'customer',
            'status' => 'active',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Login Otomatis
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        $request->session()->regenerate();

        return $this->redirectByRole()
            ->with('success', 'Akun berhasil dibuat. Selamat datang di Ragen Swim Course!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Anda berhasil keluar dari akun.'
            );
    }

    /**
     * Membuat key rate limiter berdasarkan email + IP.
     */
    private function loginThrottleKey(
        string $email,
        Request $request
    ): string {
        return Str::transliterate(
            Str::lower($email)
            . '|'
            . $request->ip()
        );
    }

    /**
     * Mengarahkan user sesuai role.
     */
    private function redirectByRole(): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()
                ->route('login');
        }

        // Pertahankan pilihan paket dari katalog publik setelah login.
        // Hanya URL internal halaman pemilihan paket yang boleh digunakan.
        if ($user->role === 'customer') {
            $intended = request()->session()->get('url.intended');
            if (is_string($intended)) {
                $parts = parse_url($intended);
                $browseParts = parse_url(route('customer.packages.browse'));
                $sameHost = isset($parts['host'], $browseParts['host'])
                    && strcasecmp($parts['host'], $browseParts['host']) === 0;
                $samePort = ($parts['port'] ?? null) === ($browseParts['port'] ?? null);
                $noCredentials = ! isset($parts['user'], $parts['pass'])
                    && ! isset($parts['user'])
                    && ! isset($parts['pass']);
                $samePath = ($parts['path'] ?? '') === ($browseParts['path'] ?? '');
                $sameScheme = ($parts['scheme'] ?? '') === ($browseParts['scheme'] ?? '');
                if ($sameHost && $samePort && $noCredentials && $samePath && $sameScheme) {
                    return redirect()->intended(route('customer.dashboard'));
                }
            }
        }

        // Buang tujuan lama/tidak sah agar tidak terbawa ke login berikutnya.
        request()->session()->forget('url.intended');

        return match ($user->role) {
            'admin' =>
                redirect()
                    ->route('admin.dashboard'),

            'coach' =>
                redirect()
                    ->route('coach.dashboard'),

            'customer' =>
                redirect()
                    ->route('customer.dashboard'),

            default =>
                $this->logoutInvalidRole(),
        };
    }

    /**
     * Logout apabila role user tidak dikenali.
     */
    private function logoutInvalidRole(): RedirectResponse
    {
        Auth::logout();

        request()
            ->session()
            ->invalidate();

        request()
            ->session()
            ->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' =>
                    'Role akun tidak dikenali oleh sistem.',
            ]);
    }
}
