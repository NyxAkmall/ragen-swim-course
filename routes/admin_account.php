<?php

use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pengaturan Akun Admin
|--------------------------------------------------------------------------
|
| File ini dipanggil dari dalam grup route Admin pada web.php.
| Middleware auth dan role:admin mengikuti grup tersebut.
|
*/

Route::get(
    '/profile',
    [AdminProfileController::class, 'edit']
)->name('profile.edit');

Route::put(
    '/profile',
    [AdminProfileController::class, 'update']
)->name('profile.update');

Route::put(
    '/profile/password',
    [AdminProfileController::class, 'updatePassword']
)->name('profile.password.update');
