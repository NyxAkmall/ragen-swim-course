<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan pembayaran.
     */
    public function payment(): View
    {
        $bankAccount = Setting::paymentBankAccount();

        return view(
            'admin.settings.payment',
            [
                'bankAccount' => $bankAccount,

                'isConfigured' =>
                    Setting::hasPaymentBankAccount(),

                'registrationFee' =>
                    Setting::registrationFee(),
            ]
        );
    }

    /**
     * Memperbarui rekening transfer bank.
     *
     * Fungsi ini hanya mengubah data rekening bank.
     */
    public function updatePayment(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate(
            [
                'bank_name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'account_number' => [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[0-9]+$/',
                ],

                'account_holder' => [
                    'required',
                    'string',
                    'max:150',
                ],
            ],
            [
                'bank_name.required' =>
                    'Nama bank wajib diisi.',

                'bank_name.max' =>
                    'Nama bank maksimal 100 karakter.',

                'account_number.required' =>
                    'Nomor rekening wajib diisi.',

                'account_number.max' =>
                    'Nomor rekening maksimal 50 karakter.',

                'account_number.regex' =>
                    'Nomor rekening hanya boleh berisi angka.',

                'account_holder.required' =>
                    'Nama pemilik rekening wajib diisi.',

                'account_holder.max' =>
                    'Nama pemilik rekening maksimal 150 karakter.',
            ]
        );

        Setting::setPaymentBankAccount(
            $validated['bank_name'],
            $validated['account_number'],
            $validated['account_holder']
        );

        return redirect()
            ->route('admin.settings.payment')
            ->with(
                'success',
                'Rekening bank berhasil diperbarui.'
            );
    }

    /**
     * Memperbarui biaya pendaftaran peserta.
     *
     * Fungsi ini hanya mengubah nominal biaya pendaftaran.
     * Tidak mengubah informasi rekening bank.
     */
    public function updateRegistrationFee(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate(
            [
                'registration_fee' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:9999999999',
                ],
            ],
            [
                'registration_fee.required' =>
                    'Nominal biaya pendaftaran wajib diisi.',

                'registration_fee.integer' =>
                    'Biaya pendaftaran harus berupa angka bulat.',

                'registration_fee.min' =>
                    'Biaya pendaftaran tidak boleh negatif.',

                'registration_fee.max' =>
                    'Nominal biaya pendaftaran terlalu besar.',
            ]
        );

        Setting::setRegistrationFee(
            (int) $validated['registration_fee']
        );

        return redirect()
            ->route('admin.settings.payment')
            ->with(
                'success',
                'Biaya pendaftaran berhasil diperbarui.'
            );
    }
}
