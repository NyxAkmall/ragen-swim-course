<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => Str::lower(
                trim((string) $request->input('email', ''))
            ),
        ]);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        // Pesan umum agar keberadaan akun tidak dapat ditebak.
        $message = 'Jika alamat email terdaftar, tautan reset password akan dikirim. Silakan periksa kotak masuk dan folder spam.';

        Password::broker()->sendResetLink([
            'email' => $validated['email'],
        ]);

        return back()->with('status', $message);
    }

    public function showResetPassword(
        Request $request,
        string $token
    ): View {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => Str::lower(
                trim((string) $request->input('email', ''))
            ),
        ]);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:150'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)->letters()->numbers(),
            ],
        ], [
            'token.required' => 'Token reset password tidak tersedia.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
        ]);

        $status = Password::broker()->reset(
            $validated,
            function ($user, string $password): void {
                // Tidak menyimpan remember_token karena
                // kolom tersebut tidak ada pada tabel users.
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Tautan reset password tidak valid atau sudah kedaluwarsa. Silakan meminta tautan baru.',
                ]);
        }

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Password berhasil diperbarui. Silakan masuk menggunakan password baru.'
            );
    }
}
