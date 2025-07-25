<?php

use App\Http\Controllers\Admin\AlatTestController as AdminAlatTestController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\MyBookingListController;
use App\Http\Controllers\User\RoomListController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\BookingListController;

use App\Http\Controllers\ChangePassController;
use App\Http\Controllers\User\AlatTestListController;
use App\Http\Controllers\Admin\AlatTestController;
use App\Http\Controllers\User\AlatTestBookingController;
use Illuminate\Support\Facades\Auth;

// use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::prefix('/')
    ->get('/', [UserDashboardController::class, 'index'])
    /* 
    |--------------------------------------------------------------------------
    | Which Home Middleware
    |--------------------------------------------------------------------------
    | Which home middleware to support one login page for multiple user
    | because static var HOME which is the destnation after user login is '/'. 
    | It is checking user's role, user or admin. if someone login with 
    | user role then redirect to '/' or user's dashboard and that's it. but if  
    | the role is admin then first go to '/' which is user's dashboard and then 
    | redirect to '/admin' or admin's dashboard.
    */
    ->middleware(['auth', 'which.home'])
    ->name('user.dashboard');

Route::prefix('/')
    ->middleware(['auth', 'is.user'])
    ->group(function () {
        Route::get('/dashboard-booking-list', [UserDashboardController::class, 'dashboard_booking_list'])
            ->name('dashboard.booking-list');
        Route::get('/room/json', [RoomListController::class, 'json'])
            ->name('room-list.json');
        Route::get('/room', [RoomListController::class, 'index'])
            ->name('room-list.index');

        Route::get('/my-booking-list/json', [MyBookingListController::class, 'json'])
            ->name('my-booking-list.json');
        Route::get('/my-booking-list', [MyBookingListController::class, 'index'])
            ->name('my-booking-list.index');
        Route::get('/my-booking-list/create', [MyBookingListController::class, 'create'])
            ->name('my-booking-list.create');
        Route::post('/my-booking-list/store', [MyBookingListController::class, 'store'])
            ->name('my-booking-list.store');
        Route::put('/my-booking-list/{id}/cancel', [MyBookingListController::class, 'cancel'])
            ->name('my-booking-list.cancel');

        Route::get('/alat-test', [AlatTestListController::class, 'index'])
            ->name('alat-test.index');
        // Route::get('/mail', function () {
        //     Mail::to('fajarwindhuzulfikar@gmail.com')
        //         ->send(new \App\Mail\BookingMail('Booking Ruangan 3', 'Admin'));
        //     return 'Terkirim';
        // });
        // My Booking Alat Test List (USER)
        Route::get('/my-booking-alat-test-list', [AlatTestBookingController::class, 'index'])
            ->name('my-booking-alat-test-list.index');

        Route::get('/my-booking-alat-test-list/json', [AlatTestBookingController::class, 'json'])->name('alat-test-booking.json');

        Route::get('/my-booking-alat-test-list/create', [AlatTestBookingController::class, 'create'])
            ->name('my-booking-alat-test-list.create');

        Route::post('/my-booking-alat-test-list/store', [AlatTestBookingController::class, 'store'])
            ->name('my-booking-alat-test-list.store');

        Route::get('/my-booking-alat-test-list/{id}/edit', [AlatTestBookingController::class, 'edit'])
            ->name('my-booking-alat-test-list.edit');

        Route::put('/my-booking-alat-test-list/{id}/update', [AlatTestBookingController::class, 'update'])
            ->name('my-booking-alat-test-list.update');

        Route::delete('/my-booking-alat-test-list/{id}/delete', [AlatTestBookingController::class, 'destroy'])
            ->name('my-booking-alat-test-list.destroy');
    });

Route::prefix('admin')
    ->middleware(['auth', 'is.admin'])
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::get('/user/json', [UserController::class, 'json'])
            ->name('user.json');

        Route::get('/user/{id}/change-pass', [UserController::class, 'change_pass'])
            ->name('user.change-pass');

        Route::put('/user/{id}/update-pass', [UserController::class, 'update_pass'])
            ->name('user.update-pass');

        Route::get('/room/json', [RoomController::class, 'json'])
            ->name('room.json');

        Route::get('/booking-list/json', [BookingListController::class, 'json'])
            ->name('booking-list.json');

        Route::get('/booking-list', [BookingListController::class, 'index'])
            ->name('booking-list.index');

        Route::put('/booking-list/{id}/update/{value}', [BookingListController::class, 'update'])
            ->name('booking-list.update');

        Route::get('/alat-test/json', [AdminAlatTestController::class, 'json'])->name('alat-test.json');
        Route::resource('alat-test', AdminAlatTestController::class)->names('alat-test-admin');

        Route::resources([
            'user'          => UserController::class,
            'room'          => RoomController::class,
        ]);
    });

// Route::prefix('alat-test-booking')->middleware('auth')->group(function () {
//     Route::get('/', [AlatTestBookingController::class, 'index'])->name('alat-test-booking.index');
//     Route::get('/create', [AlatTestBookingController::class, 'create'])->name('alat-test-booking.create');
//     Route::post('/', [AlatTestBookingController::class, 'store'])->name('alat-test-booking.store');
// });

/* 
| So basically we have 2 users here, USER and ADMIN. USER prefix is '/'
| and ADMIN prefix is 'admin'. Here we have change password feature that 
| can be used by either USER nor ADMIN.
*/

$users = [
    '/',
    'admin',
];

foreach ($users as $user) {
    Route::prefix($user)
        ->middleware(['auth'])
        ->group(function () use ($user) {
            if ($user == '/') $user = 'user';
            Route::get('/change-pass', [ChangePassController::class, 'index'])
                ->name($user . '.change-pass.index');
            Route::put('/change-pass/update', [ChangePassController::class, 'update'])
                ->name($user . '.change-pass.update');
        });
}
