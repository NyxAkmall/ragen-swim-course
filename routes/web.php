<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ClassScheduleController as AdminClassScheduleController;
use App\Http\Controllers\Admin\CoachController as AdminCoachController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\WaitingListController as AdminWaitingListController;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\Coach\AttendanceController;
use App\Http\Controllers\Coach\DashboardController as CoachDashboardController;
use App\Http\Controllers\Coach\ProgressController;
use App\Http\Controllers\Coach\ProfileController as CoachProfileController;
use App\Http\Controllers\Coach\ScheduleController as CoachScheduleController;

use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\InvoiceController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PackageController;
use App\Http\Controllers\Customer\ParticipantController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\ProgressController as CustomerProgressController;
use App\Http\Controllers\Customer\WaitingListController as CustomerWaitingListController;

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentProofController;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [HomeController::class, 'index']
)->name('home');

Route::get('/paket-renang', [CatalogController::class, 'index'])->name('catalog.index');


/*
|--------------------------------------------------------------------------
| Guest Authentication
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Akses Bukti Pembayaran
|--------------------------------------------------------------------------
|
| Login wajib. Hak akses admin/customer dan kepemilikan pesanan
| diperiksa kembali di PaymentProofController.
|
*/

Route::get(
    '/payment-proofs/orders/{order}/payments/{payment}',
    [PaymentProofController::class, 'show']
)
    ->middleware('auth')
    ->whereNumber('order')
    ->whereNumber('payment')
    ->name('payment-proofs.show');

Route::middleware('guest')->group(function () {

    Route::get(
        '/login',
        [AuthController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/login',
        [AuthController::class, 'login']
    )->name('login.process');

    Route::get(
        '/register',
        [AuthController::class, 'showRegister']
    )->name('register');

    Route::post(
        '/register',
        [AuthController::class, 'register']
    )->name('register.process');

});


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post(
    '/logout',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    // 'auth.session',
    'role:admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        require __DIR__ . '/admin_account.php';
        require __DIR__ . '/admin_password_reset.php';

        /*
        |--------------------------------------------------------------------------
        | Media Website: Logo, Favicon, Gallery, Sponsor
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/media',
            [AdminMediaController::class, 'index']
        )->name('media.index');

        Route::post(
            '/media/logo',
            [AdminMediaController::class, 'logoUpdate']
        )->name('media.logo');

        Route::post(
            '/media/favicon',
            [AdminMediaController::class, 'faviconUpdate']
        )->name('media.favicon');

        Route::post(
            '/media/galleries',
            [AdminMediaController::class, 'galleryStore']
        )->name('media.gallery.store');

        Route::put(
            '/media/galleries/{gallery}',
            [AdminMediaController::class, 'galleryUpdate']
        )->name('media.gallery.update');

        Route::delete(
            '/media/galleries/{gallery}',
            [AdminMediaController::class, 'galleryDestroy']
        )->name('media.gallery.destroy');

        Route::post(
            '/media/sponsors',
            [AdminMediaController::class, 'sponsorStore']
        )->name('media.sponsor.store');

        Route::put(
            '/media/sponsors/{sponsor}',
            [AdminMediaController::class, 'sponsorUpdate']
        )->name('media.sponsor.update');

        Route::delete(
            '/media/sponsors/{sponsor}',
            [AdminMediaController::class, 'sponsorDestroy']
        )->name('media.sponsor.destroy');


        /*
        |--------------------------------------------------------------------------
        | Customer & Peserta
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/customers',
            [AdminCustomerController::class, 'index']
        )->name('customers.index');

        Route::get(
            '/customers/{customer}',
            [AdminCustomerController::class, 'show']
        )
            ->whereNumber('customer')
            ->name('customers.show');


        /*
        |--------------------------------------------------------------------------
        | Customer Status
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/customers/{customer}/status',
            [AdminCustomerController::class, 'updateStatus']
        )
            ->whereNumber('customer')
            ->name('customers.status.update');

        Route::delete(
            '/customers/{customer}',
            [\App\Http\Controllers\Admin\CustomerController::class, 'destroy']
        )->whereNumber('customer')
         ->name('customers.destroy');


        /*
        |--------------------------------------------------------------------------
        | Coach
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/coaches',
            [AdminCoachController::class, 'index']
        )->name('coaches.index');

        Route::get(
            '/coaches/create',
            [AdminCoachController::class, 'create']
        )->name('coaches.create');

        Route::post(
            '/coaches',
            [AdminCoachController::class, 'store']
        )->name('coaches.store');

        Route::get(
            '/coaches/{coach}',
            [AdminCoachController::class, 'show']
        )
            ->whereNumber('coach')
            ->name('coaches.show');

        Route::get(
            '/coaches/{coach}/edit',
            [AdminCoachController::class, 'edit']
        )
            ->whereNumber('coach')
            ->name('coaches.edit');

        Route::put(
            '/coaches/{coach}',
            [AdminCoachController::class, 'update']
        )
            ->whereNumber('coach')
            ->name('coaches.update');


        /*
        |--------------------------------------------------------------------------
        | Coach Status
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/coaches/{coach}/status',
            [AdminCoachController::class, 'updateStatus']
        )
            ->whereNumber('coach')
            ->name('coaches.status.update');

        Route::delete(
            '/coaches/{coach}',
            [\App\Http\Controllers\Admin\CoachController::class, 'destroy']
        )->whereNumber('coach')
         ->name('coaches.destroy');


        /*
        |--------------------------------------------------------------------------
        | Program
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/programs',
            [AdminProgramController::class, 'index']
        )->name('programs.index');

        Route::get(
            '/programs/create',
            [AdminProgramController::class, 'create']
        )->name('programs.create');

        Route::post(
            '/programs',
            [AdminProgramController::class, 'store']
        )->name('programs.store');

        Route::get(
            '/programs/{program}',
            [AdminProgramController::class, 'show']
        )
            ->whereNumber('program')
            ->name('programs.show');

        Route::get(
            '/programs/{program}/edit',
            [AdminProgramController::class, 'edit']
        )
            ->whereNumber('program')
            ->name('programs.edit');

        Route::put(
            '/programs/{program}',
            [AdminProgramController::class, 'update']
        )
            ->whereNumber('program')
            ->name('programs.update');


        /*
        |--------------------------------------------------------------------------
        | Program Status
        |--------------------------------------------------------------------------
        */

        Route::delete('/programs/{program}', [AdminProgramController::class, 'destroy'])->whereNumber('program')->name('programs.destroy');

        Route::patch(
            '/programs/{program}/status',
            [AdminProgramController::class, 'updateStatus']
        )
            ->whereNumber('program')
            ->name('programs.status.update');


        /*
        |--------------------------------------------------------------------------
        | Package
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/packages',
            [AdminPackageController::class, 'index']
        )->name('packages.index');

        Route::get(
            '/packages/create',
            [AdminPackageController::class, 'create']
        )->name('packages.create');

        Route::post(
            '/packages',
            [AdminPackageController::class, 'store']
        )->name('packages.store');

        Route::get(
            '/packages/{package}',
            [AdminPackageController::class, 'show']
        )
            ->whereNumber('package')
            ->name('packages.show');

        Route::get(
            '/packages/{package}/edit',
            [AdminPackageController::class, 'edit']
        )
            ->whereNumber('package')
            ->name('packages.edit');

        Route::put(
            '/packages/{package}',
            [AdminPackageController::class, 'update']
        )
            ->whereNumber('package')
            ->name('packages.update');


        /*
        |--------------------------------------------------------------------------
        | Package Status
        |--------------------------------------------------------------------------
        */

        Route::delete('/packages/{package}', [AdminPackageController::class, 'destroy'])->whereNumber('package')->name('packages.destroy');

        Route::patch(
            '/packages/{package}/status',
            [AdminPackageController::class, 'updateStatus']
        )
            ->whereNumber('package')
            ->name('packages.status.update');


        /*
        |--------------------------------------------------------------------------
        | Location
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/locations',
            [AdminLocationController::class, 'index']
        )->name('locations.index');

        Route::get(
            '/locations/create',
            [AdminLocationController::class, 'create']
        )->name('locations.create');

        Route::post(
            '/locations',
            [AdminLocationController::class, 'store']
        )->name('locations.store');

        Route::get(
            '/locations/{location}',
            [AdminLocationController::class, 'show']
        )
            ->whereNumber('location')
            ->name('locations.show');

        Route::get(
            '/locations/{location}/edit',
            [AdminLocationController::class, 'edit']
        )
            ->whereNumber('location')
            ->name('locations.edit');

        Route::put(
            '/locations/{location}',
            [AdminLocationController::class, 'update']
        )
            ->whereNumber('location')
            ->name('locations.update');


        /*
        |--------------------------------------------------------------------------
        | Location Status
        |--------------------------------------------------------------------------
        */

        Route::delete('/locations/{location}', [AdminLocationController::class, 'destroy'])->whereNumber('location')->name('locations.destroy');

        Route::patch(
            '/locations/{location}/status',
            [AdminLocationController::class, 'updateStatus']
        )
            ->whereNumber('location')
            ->name('locations.status.update');


        /*
        |--------------------------------------------------------------------------
        | Class Schedule / Jadwal Kelas
        |--------------------------------------------------------------------------
        */

        // Weekly Schedule / Jadwal Mingguan
        Route::get('/weekly-schedules', [\App\Http\Controllers\Admin\WeeklyScheduleController::class, 'index'])
            ->name('weekly-schedules.index');

        Route::get('/weekly-schedules/create', [\App\Http\Controllers\Admin\WeeklyScheduleController::class, 'create'])
            ->name('weekly-schedules.create');

        Route::post('/weekly-schedules', [\App\Http\Controllers\Admin\WeeklyScheduleController::class, 'store'])
            ->name('weekly-schedules.store');

        Route::get('/weekly-schedules/{weeklySchedule}/edit', [\App\Http\Controllers\Admin\WeeklyScheduleController::class, 'edit'])
            ->name('weekly-schedules.edit');

        Route::put('/weekly-schedules/{weeklySchedule}', [\App\Http\Controllers\Admin\WeeklyScheduleController::class, 'update'])
            ->name('weekly-schedules.update');

        Route::patch('/weekly-schedules/{weeklySchedule}/status', [\App\Http\Controllers\Admin\WeeklyScheduleController::class, 'updateStatus'])
            ->name('weekly-schedules.status.update');

        Route::delete(
            '/weekly-schedules/{weeklySchedule}',
            [\App\Http\Controllers\Admin\WeeklyScheduleController::class, 'destroy']
        )->whereNumber('weeklySchedule')
         ->name('weekly-schedules.destroy');

        Route::get(
            '/class-schedules',
            [AdminClassScheduleController::class, 'index']
        )->name('class-schedules.index');

        Route::get(
            '/class-schedules/create',
            [AdminClassScheduleController::class, 'create']
        )->name('class-schedules.create');

        Route::post(
            '/class-schedules',
            [AdminClassScheduleController::class, 'store']
        )->name('class-schedules.store');

        Route::get(
            '/class-schedules/{classSchedule}',
            [AdminClassScheduleController::class, 'show']
        )
            ->whereNumber('classSchedule')
            ->name('class-schedules.show');

        Route::get(
            '/class-schedules/{classSchedule}/edit',
            [AdminClassScheduleController::class, 'edit']
        )
            ->whereNumber('classSchedule')
            ->name('class-schedules.edit');

        Route::put(
            '/class-schedules/{classSchedule}',
            [AdminClassScheduleController::class, 'update']
        )
            ->whereNumber('classSchedule')
            ->name('class-schedules.update');


        /*
        |--------------------------------------------------------------------------
        | Class Schedule Status
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/class-schedules/{classSchedule}/status',
            [AdminClassScheduleController::class, 'updateStatus']
        )
            ->whereNumber('classSchedule')
            ->name('class-schedules.status.update');

        Route::delete(
            '/class-schedules/{classSchedule}',
            [AdminClassScheduleController::class, 'destroy']
        )->whereNumber('classSchedule')
         ->name('class-schedules.destroy');


        /*
        |--------------------------------------------------------------------------
        | Booking
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/bookings',
            [AdminBookingController::class, 'index']
        )->name('bookings.index');

        Route::get(
            '/bookings/{booking}',
            [AdminBookingController::class, 'show']
        )
            ->whereNumber('booking')
            ->name('bookings.show');


        /*
        |--------------------------------------------------------------------------
        | Booking Confirmation
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/bookings/{booking}/confirm',
            [AdminBookingController::class, 'confirm']
        )
            ->whereNumber('booking')
            ->name('bookings.confirm');


        /*
        |--------------------------------------------------------------------------
        | Booking Cancellation
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/bookings/{booking}/cancel',
            [AdminBookingController::class, 'cancel']
        )
            ->whereNumber('booking')
            ->name('bookings.cancel');


        /*
        |--------------------------------------------------------------------------
        | Waiting List Manual - Admin
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/waiting-lists',
            [AdminWaitingListController::class, 'index']
        )->name('waiting-lists.index');

        Route::post(
            '/waiting-lists/{waitingList}/offer',
            [AdminWaitingListController::class, 'offer']
        )
            ->whereNumber('waitingList')
            ->name('waiting-lists.offer');

        Route::get(
            '/waiting-lists/{waitingList}',
            [AdminWaitingListController::class, 'show']
        )
            ->whereNumber('waitingList')
            ->name('waiting-lists.show');

        Route::post(
            '/waiting-lists/{waitingList}/cancel',
            [AdminWaitingListController::class, 'cancel']
        )
            ->whereNumber('waitingList')
            ->name('waiting-lists.cancel');


        /*
        |--------------------------------------------------------------------------
        | Order & Pembayaran
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/orders',
            [AdminOrderController::class, 'index']
        )->name('orders.index');

        Route::get(
            '/orders/{order}',
            [AdminOrderController::class, 'show']
        )
            ->whereNumber('order')
            ->name('orders.show');


        /*
        |--------------------------------------------------------------------------
        | Verifikasi Pembayaran
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/orders/{order}/payments/{payment}/verify',
            [AdminOrderController::class, 'verifyPayment']
        )
            ->whereNumber('order')
            ->whereNumber('payment')
            ->name('orders.payments.verify');


        /*
        |--------------------------------------------------------------------------
        | Tolak Pembayaran
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/orders/{order}/payments/{payment}/reject',
            [AdminOrderController::class, 'rejectPayment']
        )
            ->whereNumber('order')
            ->whereNumber('payment')
            ->name('orders.payments.reject');


        /*
        |--------------------------------------------------------------------------
        | Invoice Admin
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/invoices',
            [AdminInvoiceController::class, 'index']
        )->name('invoices.index');

        Route::get(
            '/invoices/{invoice}',
            [AdminInvoiceController::class, 'show']
        )
            ->whereNumber('invoice')
            ->name('invoices.show');


        /*
        |--------------------------------------------------------------------------
        | Laporan Transaksi Admin
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [AdminReportController::class, 'index']
        )->name('reports.index');

        Route::get(
            '/reports/export',
            [AdminReportController::class, 'export']
        )->name('reports.export');


        /*
        |--------------------------------------------------------------------------
        | Pengaturan Pembayaran
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/settings/payment',
            [AdminSettingController::class, 'payment']
        )->name('settings.payment');

        Route::put(
            '/settings/payment',
            [AdminSettingController::class, 'updatePayment']
        )->name('settings.payment.update');


        /*
        |--------------------------------------------------------------------------
        | Biaya Pendaftaran Peserta
        |--------------------------------------------------------------------------
        */

        Route::put(
            '/settings/registration-fee',
            [AdminSettingController::class, 'updateRegistrationFee']
        )->name('settings.registration-fee.update');


        /*
        |--------------------------------------------------------------------------
        | FAQ / Pertanyaan Umum
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/faqs',
            [AdminFaqController::class, 'index']
        )->name('faqs.index');

        Route::get(
            '/faqs/create',
            [AdminFaqController::class, 'create']
        )->name('faqs.create');

        Route::post(
            '/faqs',
            [AdminFaqController::class, 'store']
        )->name('faqs.store');

        Route::get(
            '/faqs/{faq}/edit',
            [AdminFaqController::class, 'edit']
        )
            ->whereNumber('faq')
            ->name('faqs.edit');

        Route::put(
            '/faqs/{faq}',
            [AdminFaqController::class, 'update']
        )
            ->whereNumber('faq')
            ->name('faqs.update');

        Route::patch(
            '/faqs/{faq}/status',
            [AdminFaqController::class, 'updateStatus']
        )
            ->whereNumber('faq')
            ->name('faqs.status.update');

        Route::delete(
            '/faqs/{faq}',
            [AdminFaqController::class, 'destroy']
        )
            ->whereNumber('faq')
            ->name('faqs.destroy');

    });


/*
|--------------------------------------------------------------------------
| Coach
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    // 'auth.session',
    'role:coach',
])
    ->prefix('coach')
    ->name('coach.')
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [CoachDashboardController::class, 'index']
        )->name('dashboard');

        Route::get('/profile', [CoachProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/photo', [CoachProfileController::class, 'update'])->name('profile.update');

        require __DIR__ . '/coach_account.php';


        /*
        |--------------------------------------------------------------------------
        | Perkembangan Peserta
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/progress',
            [ProgressController::class, 'index']
        )->name('progress.index');


        /*
        |--------------------------------------------------------------------------
        | Jadwal Coach
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/schedules',
            [CoachScheduleController::class, 'index']
        )->name('schedules.index');

        Route::get(
            '/schedules/{schedule}',
            [CoachScheduleController::class, 'show']
        )
            ->whereNumber('schedule')
            ->name('schedules.show');


        /*
        |--------------------------------------------------------------------------
        | Attendance
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/attendance',
            [AttendanceController::class, 'index']
        )->name('attendance.index');

        Route::post(
            '/bookings/{booking}/attendance',
            [AttendanceController::class, 'store']
        )
            ->whereNumber('booking')
            ->name('attendance.store');


        /*
        |--------------------------------------------------------------------------
        | Progress / Penilaian Peserta
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/bookings/{booking}/progress',
            [ProgressController::class, 'edit']
        )
            ->whereNumber('booking')
            ->name('progress.edit');

        Route::put(
            '/bookings/{booking}/progress',
            [ProgressController::class, 'update']
        )
            ->whereNumber('booking')
            ->name('progress.update');

    });


/*
|--------------------------------------------------------------------------
| Customer
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    // 'auth.session',
    'role:customer',
])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [CustomerDashboardController::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Participants
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/participants',
            [ParticipantController::class, 'index']
        )->name('participants.index');

        Route::get(
            '/participants/create',
            [ParticipantController::class, 'create']
        )->name('participants.create');

        Route::post(
            '/participants',
            [ParticipantController::class, 'store']
        )->name('participants.store');

        Route::get(
            '/participants/{participant}/edit',
            [ParticipantController::class, 'edit']
        )
            ->whereNumber('participant')
            ->name('participants.edit');

        Route::put(
            '/participants/{participant}',
            [ParticipantController::class, 'update']
        )
            ->whereNumber('participant')
            ->name('participants.update');

        Route::delete(
            '/participants/{participant}',
            [ParticipantController::class, 'destroy']
        )
            ->whereNumber('participant')
            ->name('participants.destroy');


        /*
        |--------------------------------------------------------------------------
        | Packages
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/packages/browse',
            [PackageController::class, 'browse']
        )->name('packages.browse');

        Route::get(
            '/packages',
            [PackageController::class, 'index']
        )->name('packages.index');

        Route::get(
            '/packages/{participantPackage}',
            [PackageController::class, 'show']
        )
            ->whereNumber('participantPackage')
            ->name('packages.show');


        /*
        |--------------------------------------------------------------------------
        | Bookings
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/bookings',
            [BookingController::class, 'index']
        )->name('bookings.index');

        Route::post(
            '/bookings',
            [BookingController::class, 'store']
        )->name('bookings.store');


        /*
        |--------------------------------------------------------------------------
        | Booking Reschedule
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/bookings/{booking}/reschedule',
            [BookingController::class, 'reschedule']
        )
            ->whereNumber('booking')
            ->name('bookings.reschedule');

        Route::post(
            '/bookings/{booking}/reschedule',
            [BookingController::class, 'processReschedule']
        )
            ->whereNumber('booking')
            ->name('bookings.reschedule.process');


        /*
        |--------------------------------------------------------------------------
        | Booking Cancellation
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/bookings/{booking}/cancel',
            [BookingController::class, 'cancel']
        )
            ->whereNumber('booking')
            ->name('bookings.cancel');


        /*
        |--------------------------------------------------------------------------
        | Booking Detail
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/bookings/{booking}',
            [BookingController::class, 'show']
        )
            ->whereNumber('booking')
            ->name('bookings.show');


        /*
        |--------------------------------------------------------------------------
        | Waiting List Manual - Customer
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/waiting-lists',
            [CustomerWaitingListController::class, 'store']
        )->name('waiting-lists.store');

        Route::delete(
            '/waiting-lists/{waitingList}',
            [CustomerWaitingListController::class, 'destroy']
        )
            ->whereNumber('waitingList')
            ->name('waiting-lists.destroy');

        Route::post(
            '/waiting-lists/{waitingList}/accept',
            [CustomerWaitingListController::class, 'accept']
        )
            ->whereNumber('waitingList')
            ->name('waiting-lists.accept');

        Route::post(
            '/waiting-lists/{waitingList}/decline',
            [CustomerWaitingListController::class, 'decline']
        )
            ->whereNumber('waitingList')
            ->name('waiting-lists.decline');


        /*
        |--------------------------------------------------------------------------
        | Progress / Perkembangan Peserta
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/progress',
            [CustomerProgressController::class, 'index']
        )->name('progress.index');


        /*
        |--------------------------------------------------------------------------
        | Checkout
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/checkout',
            [CheckoutController::class, 'show']
        )->name('checkout.show');

        Route::post(
            '/checkout',
            [CheckoutController::class, 'store']
        )->name('checkout.store');


        /*
        |--------------------------------------------------------------------------
        | Orders / Pembayaran
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/orders',
            [OrderController::class, 'index']
        )->name('orders.index');

        Route::get(
            '/orders/{order}',
            [OrderController::class, 'show']
        )
            ->whereNumber('order')
            ->name('orders.show');


        /*
        |--------------------------------------------------------------------------
        | Manual Bank Transfer
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/orders/{order}/payment/manual-transfer',
            [PaymentController::class, 'prepareManualTransfer']
        )
            ->whereNumber('order')
            ->name('payments.manual-transfer');


        /*
        |--------------------------------------------------------------------------
        | Upload Bukti Pembayaran
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/orders/{order}/payment/upload-proof',
            [PaymentController::class, 'uploadProof']
        )
            ->whereNumber('order')
            ->name('payments.upload-proof');


        /*
        |--------------------------------------------------------------------------
        | Invoice Customer
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/invoices',
            [InvoiceController::class, 'index']
        )->name('invoices.index');

        Route::get(
            '/invoices/{invoice}',
            [InvoiceController::class, 'show']
        )
            ->whereNumber('invoice')
            ->name('invoices.show');


        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/profile',
            [ProfileController::class, 'edit']
        )->name('profile.edit');

        Route::put(
            '/profile',
            [ProfileController::class, 'update']
        )->name('profile.update');

        Route::put(
            '/profile/password',
            [ProfileController::class, 'updatePassword']
        )->name('profile.password.update');

    });

require __DIR__ . '/password.php';
