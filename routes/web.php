<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FineController as AdminFineController;
use App\Http\Controllers\Admin\MotorController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\FineController as CustomerFineController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
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

    // Customer Fines
    Route::get('/my-fines', [CustomerFineController::class, 'index'])->name('customer.fines.index');
    Route::get('/fines/{fine}/payment', [CustomerFineController::class, 'showPayment'])->name('customer.fines.payment');
    Route::post('/fines/{fine}/payment/proof', [CustomerFineController::class, 'uploadProof'])->name('customer.fines.uploadProof');
    Route::post('/fines/{fine}/pay', [CustomerFineController::class, 'pay'])->name('customer.fines.pay');
    Route::post('/bookings/{booking}/pay-all-fines', [CustomerFineController::class, 'payAll'])->name('customer.fines.payAll');

    // Reviews
    Route::post('/booking-items/{bookingItem}/review', [CustomerReviewController::class, 'store'])->name('customer.reviews.store');
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
    Route::post('/bookings/{booking}/items/{item}/return', [AdminBookingController::class, 'confirmReturn'])->name('bookings.return');
    Route::post('/bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');

    // Fine Management
    Route::get('/fines', [AdminFineController::class, 'index'])->name('fines.index');
    Route::post('/fines', [AdminFineController::class, 'store'])->name('fines.store');
    Route::post('/fines/{fine}/verify', [AdminFineController::class, 'verifyPayment'])->name('fines.verify');
    Route::post('/fines/{fine}/reject', [AdminFineController::class, 'rejectPayment'])->name('fines.reject');

    // Review Management (Disabled from active admin UI)
    Route::get('/reviews', fn () => abort(404))->name('reviews.index');
});
