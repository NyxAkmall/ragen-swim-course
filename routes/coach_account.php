<?php

use App\Http\Controllers\Coach\ProfileController as CoachProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pengaturan Akun Coach
|--------------------------------------------------------------------------
|
| File ini dimuat dari dalam kelompok rute coach di routes/web.php.
| Middleware auth dan role:coach diwarisi dari kelompok tersebut.
|
*/

Route::put(
    '/profile/account',
    [CoachProfileController::class, 'updateAccount']
)->name('profile.account.update');

Route::put(
    '/profile/password',
    [CoachProfileController::class, 'updatePassword']
)->name('profile.password.update');
