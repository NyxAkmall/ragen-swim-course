<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Throwable;

class AdminPasswordResetController extends Controller
{
    /**
     * Mengirim tautan reset password kepada Customer.
     */
    public function sendCustomer(User $customer): RedirectResponse
    {
        abort_unless(
            $customer->role === 'customer',
            404
        );

        return $this->sendResetLink($customer);
    }

    /**
     * Mengirim tautan reset password kepada Coach.
     *
     * Parameter route adalah ID profil Coach,
     * bukan ID akun User.
     */
    public function sendCoach(Coach $coach): RedirectResponse
    {
        $user = $coach->user;

        abort_unless(
            $user !== null && $user->role === 'coach',
            404
        );

        return $this->sendResetLink($user);
    }

    /**
     * Mengirim tautan melalui Laravel Password Broker.
     */
    private function sendResetLink(User $user): RedirectResponse
    {
        try {
            $status = Password::broker()->sendResetLink([
                'email' => $user->email,
            ]);
        } catch (Throwable $exception) {
            Log::error('Gagal memproses reset password dari Admin.', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            return back()->with(
                'warning',
                'Tautan reset password belum berhasil dikirim. Periksa konfigurasi email atau coba kembali nanti.'
            );
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->with(
                'warning',
                'Tautan reset password baru saja diminta. Tunggu setidaknya 60 detik sebelum mencoba kembali.'
            );
        }

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->with(
                'warning',
                'Tautan reset password belum berhasil dikirim. Silakan coba kembali atau periksa konfigurasi email.'
            );
        }

        return back()->with(
            'success',
            'Permintaan pengiriman tautan reset password berhasil diproses untuk ' . $user->name . '.'
        );
    }
}