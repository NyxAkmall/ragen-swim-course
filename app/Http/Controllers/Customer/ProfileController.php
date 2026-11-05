<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman pengaturan profil customer.
     */
    public function edit(Request $request): View
    {
        return view('customer.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui nama, nomor telepon, dan email customer.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Normalisasi email sebelum validasi.
        $request->merge([
            'email' => Str::lower(
                trim((string) $request->input('email', ''))
            ),
        ]);

        // Validasi informasi profil.
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'current_password' => [
                'nullable',
                'string',
            ],
        ], [
            'name.required' =>
                'Nama lengkap wajib diisi.',
            'email.required' =>
                'Email wajib diisi.',
            'email.email' =>
                'Format email tidak valid.',
            'email.unique' =>
                'Email tersebut sudah digunakan akun lain.',
        ]);

        // Periksa apakah email berubah.
        $emailChanged = $validated['email'] !== $user->email;

        // Perubahan email wajib menggunakan password lama.
        if ($emailChanged) {
            $currentPassword = $validated['current_password'] ?? null;

            if (
                ! is_string($currentPassword)
                || $currentPassword === ''
                || ! Hash::check(
                    $currentPassword,
                    $user->password
                )
            ) {
                return back()
                    ->withErrors([
                        'current_password' =>
                            'Password saat ini wajib diisi dan harus benar untuk mengganti email.',
                    ])
                    ->withInput(
                        $request->except('current_password')
                    );
            }
        }

        // Simpan informasi profil.
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Demi keamanan, logout setelah email berubah.
        if ($emailChanged) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Email akun berhasil diperbarui. Silakan login kembali menggunakan email baru.'
                );
        }

        return redirect()
            ->route('customer.profile.edit')
            ->with(
                'success',
                'Informasi profil berhasil diperbarui.'
            );
    }

    /**
     * Mengganti password akun customer.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ], [
            'current_password.required' =>
                'Password saat ini wajib diisi.',
            'password.required' =>
                'Password baru wajib diisi.',
            'password.confirmed' =>
                'Konfirmasi password baru tidak sama.',
        ]);

        // Password lama harus benar.
        if (
            ! Hash::check(
                $validated['current_password'],
                $user->password
            )
        ) {
            return back()
                ->withErrors([
                    'current_password' =>
                        'Password saat ini tidak sesuai.',
                ]);
        }

        // Model User menggunakan cast "hashed".
        $user->password = $validated['password'];
        $user->save();

        return redirect()
            ->route('customer.profile.edit')
            ->with(
                'success',
                'Password akun berhasil diperbarui.'
            );
    }
}
