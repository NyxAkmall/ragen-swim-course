<?php

use App\Http\Controllers\Admin\AdminPasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin - Kirim Link Reset Password
|--------------------------------------------------------------------------
|
| File ini harus dimuat di dalam grup route Admin
| yang menggunakan middleware auth dan role:admin.
|
*/

Route::post(
    '/customers/{customer}/send-password-reset',
    [AdminPasswordResetController::class, 'sendCustomer']
)
    ->middleware('throttle:5,1')
    ->whereNumber('customer')
    ->name('customers.password-reset.send');

Route::post(
    '/coaches/{coach}/send-password-reset',
    [AdminPasswordResetController::class, 'sendCoach']
)
    ->middleware('throttle:5,1')
    ->whereNumber('coach')
    ->name('coaches.password-reset.send');
