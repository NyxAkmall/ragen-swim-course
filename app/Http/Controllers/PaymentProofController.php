<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaymentProofController extends Controller
{
    /**
     * Menampilkan bukti pembayaran kepada pengguna berwenang.
     *
     * Mendukung file privat dan file lama selama masa transisi.
     */
    public function show(
        Request $request,
        Order $order,
        Payment $payment
    ): BinaryFileResponse {
        $user = $request->user();

        abort_unless($user !== null, 403);

        abort_unless(
            in_array($user->role, ['admin', 'customer'], true),
            403
        );

        if ($user->role === 'customer') {
            abort_unless(
                (int) $order->user_id === (int) $user->id,
                404
            );
        }

        abort_unless(
            (int) $payment->order_id === (int) $order->id,
            404
        );

        abort_unless(
            $payment->isManualTransfer()
                && $payment->hasProof(),
            404
        );

        $path = $payment->proof_image;

        // Hanya izinkan file dari folder bukti pembayaran.
        abort_unless(
            is_string($path)
                && str_starts_with($path, 'payment-proofs/')
                && !str_contains($path, '..')
                && !str_contains($path, '\\'),
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Penyimpanan Privat
        |--------------------------------------------------------------------------
        */

        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            /*
            |--------------------------------------------------------------------------
            | Kompatibilitas Bukti Pembayaran Lama
            |--------------------------------------------------------------------------
            |
            | File lama masih berada pada disk public.
            | Fallback ini bersifat sementara.
            |
            */

            $disk = Storage::disk('public');
        }

        abort_unless($disk->exists($path), 404);

        $mimeType = $disk->mimeType($path);

        abort_unless(
            in_array(
                $mimeType,
                ['image/jpeg', 'image/png', 'image/webp'],
                true
            ),
            404
        );

        $response = response()->file(
            $disk->path($path),
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );

        $response->setPrivate();
        $response->headers->set(
            'Cache-Control',
            'private, no-store, max-age=0'
        );

        return $response;
    }
}