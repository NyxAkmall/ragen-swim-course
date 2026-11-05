<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil coach.
     */
    public function edit(Request $request): View
    {
        $coach = Coach::query()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('coach.profile.edit', compact('coach'));
    }

    /**
     * Memperbarui foto profil coach.
     */
    public function update(Request $request): RedirectResponse
    {
        $coach = Coach::query()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'photo' => [
                'required',
                'image',
                'mimes:jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $newPath = $validated['photo']->store('coaches', 'public');
        $oldPath = $coach->photo;

        try {
            $coach->update([
                'photo' => $newPath,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($newPath);

            throw $exception;
        }

        // Jangan hapus foto yang masih digunakan coach lain.
        // Hanya hapus file dari folder unggahan coaches.
        if (
            $oldPath
            && $oldPath !== $newPath
            && str_starts_with($oldPath, 'coaches/')
            && ! str_contains($oldPath, '..')
            && ! Coach::query()
                ->where('photo', $oldPath)
                ->exists()
        ) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()
            ->route('coach.profile.edit')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Memperbarui nama dan email akun coach.
     */
    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->merge([
            'email' => mb_strtolower(
                trim((string) $request->input('email'))
            ),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password' => [
                'nullable',
                'string',
            ],
        ]);

        $emailChanged = $validated['email'] !== $user->email;

        if ($emailChanged) {
            if (
                empty($validated['current_password'])
                || ! Hash::check(
                    $validated['current_password'],
                    $user->password
                )
            ) {
                return back()
                    ->withErrors([
                        'current_password' =>
                            'Password saat ini wajib diisi dan harus benar untuk mengganti email.',
                    ])
                    ->withInput($request->except('current_password'));
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Email berhasil diperbarui. Silakan login kembali menggunakan email baru.'
                );
        }

        return redirect()
            ->route('coach.profile.edit')
            ->with('success', 'Informasi akun berhasil diperbarui.');
    }

    /**
     * Mengganti password akun coach.
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

        if (
            ! Hash::check(
                $validated['current_password'],
                $user->password
            )
        ) {
            return back()->withErrors([
                'current_password' =>
                    'Password saat ini tidak sesuai.',
            ]);
        }

        // Model User menggunakan cast "hashed".
        $user->password = $validated['password'];
        $user->save();

        return redirect()
            ->route('coach.profile.edit')
            ->with('success', 'Password akun berhasil diperbarui.');
    }
}
