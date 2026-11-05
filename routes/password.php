<?php

use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Password Reset Routes - Ragen Swim Course
|--------------------------------------------------------------------------
|
| Route ini digunakan oleh seluruh pengguna:
| - Admin
| - Coach
| - Customer
|
| Pengguna yang belum login dapat meminta tautan
| reset password dan membuat password baru.
|
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Halaman Lupa Password
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/forgot-password',
        [PasswordResetController::class, 'showForgotPassword']
    )->name('password.request');

    /*
    |--------------------------------------------------------------------------
    | Kirim Email Reset Password
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/forgot-password',
        [PasswordResetController::class, 'sendResetLink']
    )
        ->middleware('throttle:5,1')
        ->name('password.email');

    /*
    |--------------------------------------------------------------------------
    | Halaman Password Baru
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reset-password/{token}',
        [PasswordResetController::class, 'showResetPassword']
    )->name('password.reset');

    /*
    |--------------------------------------------------------------------------
    | Simpan Password Baru
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/reset-password',
        [PasswordResetController::class, 'resetPassword']
    )
        ->middleware('throttle:5,1')
        ->name('password.update');

});
