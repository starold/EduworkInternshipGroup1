<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MotorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kendaraan', [HomeController::class, 'vehicles'])->name('vehicles.index');
Route::get('/motors/{motor}', [HomeController::class, 'show'])->name('motors.show');

Route::middleware('auth')->group(function () {
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('customer.bookings.index');
    Route::get('/motors/{motor}/book', [BookingController::class, 'create'])->name('customer.bookings.create');
    Route::post('/motors/{motor}/book', [BookingController::class, 'store'])->name('customer.bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('customer.bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('customer.bookings.cancel');

    // Payments
    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'show'])->name('customer.payments.show');
    Route::post('/bookings/{booking}/payment/proof', [PaymentController::class, 'uploadProof'])->name('customer.payments.upload');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Motors
    Route::resource('motors', MotorController::class)->except(['show']);

    // Bookings
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');
});