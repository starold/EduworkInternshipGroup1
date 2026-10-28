<?php

use App\Enums\BookingStatus;
use App\Enums\FineStatus;
use App\Enums\FineType;
use App\Enums\MotorStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReturnStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Fine;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use App\Services\BookingCompletionService;
use App\Services\ReturnConfirmationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// --- Helper Functions ---

function createTestMotorItem(float $pricePerDay = 100000): Motor
{
    $category = MotorCategory::firstOrCreate(
        ['slug' => 'matic'],
        ['name' => 'Matic', 'description' => 'Motor Matic']
    );

    return Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Honda',
        'model' => 'Vario 160',
        'warna' => 'Hitam',
        'year' => 2024,
        'plate_number' => 'AB ' . rand(1000, 9999) . ' XY',
        'price_per_day' => $pricePerDay,
        'deposit' => 200000,
        'status' => MotorStatus::Available,
    ]);
}

function createRoleTestBooking(User $user, Motor $motor, string $status = 'pending'): Booking
{
    $booking = Booking::create([
        'user_id' => $user->id,
        'booking_code' => 'BK-' . strtoupper(uniqid()),
        'start_date' => Carbon::parse('2026-10-01'),
        'end_date' => Carbon::parse('2026-10-03'),
        'pickup_method' => 'pickup',
        'subtotal' => 200000,
        'delivery_fee' => 0,
        'discount' => 0,
        'total_price' => 200000,
        'status' => $status,
    ]);

    $booking->items()->create([
        'motor_id' => $motor->id,
        'price_per_day' => 100000,
        'due_date' => Carbon::parse('2026-10-03 10:00:00'),
        'total_days' => 2,
        'subtotal' => 200000,
        'deposit' => 200000,
        'return_status' => ReturnStatus::NotReturned,
    ]);

    return $booking->fresh(['items.motor.category']);
}

/*
|--------------------------------------------------------------------------
| FEEDBACK 1: USER - REVIEW GATING ON "MENUNGGU KONFIRMASI PEMBAYARAN"
|--------------------------------------------------------------------------
*/

test('user cannot review when booking is pending or awaiting payment confirmation', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotorItem();
    $booking = createRoleTestBooking($customer, $motor, BookingStatus::Pending->value);
    $item = $booking->items->first();

    // Create payment awaiting confirmation
    Payment::create([
        'booking_id' => $booking->id,
        'amount' => 200000,
        'payment_method' => 'bank_transfer',
        'status' => PaymentStatus::Pending,
        'payment_proof' => 'proofs/test.jpg',
    ]);

    // Backend endpoint POST review must be rejected with 403 Forbidden
    $response = $this->actingAs($customer)->post("/booking-items/{$item->id}/review", [
        'rating' => 5,
        'comment' => 'Mencoba review saat menunggu konfirmasi pembayaran',
    ]);

    $response->assertForbidden();
    expect(Review::where('booking_items_id', $item->id)->exists())->toBeFalse();
});

test('review form is not displayed on booking show page when status is waiting for payment confirmation', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotorItem();
    $booking = createRoleTestBooking($customer, $motor, BookingStatus::Pending->value);
    $item = $booking->items->first();

    Payment::create([
        'booking_id' => $booking->id,
        'amount' => 200000,
        'payment_method' => 'bank_transfer',
        'status' => PaymentStatus::Pending,
        'payment_proof' => 'proofs/test.jpg',
    ]);

    $response = $this->actingAs($customer)->get(route('customer.bookings.show', $booking));

    $response->assertOk();
    // Form review must NOT be visible
    $response->assertDontSee('Kirim Review');
    // Lock indicator must be visible
    $response->assertSee('Form review belum tersedia karena pembayaran masih menunggu konfirmasi');
});

test('user cannot review when fine payment is waiting for admin verification', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotorItem();
    $booking = createRoleTestBooking($customer, $motor, BookingStatus::Confirmed->value);
    $item = $booking->items->first();

    // Motor is returned
    app(ReturnConfirmationService::class)->confirmReturn($item, Carbon::parse('2026-10-03 10:00:00'));

    // Fine created
    $fine = Fine::create([
        'booking_item_id' => $item->id,
        'type' => FineType::Late,
        'amount' => 50000,
        'description' => 'Denda keterlambatan',
        'status' => FineStatus::Unpaid,
    ]);

    // Fine payment uploaded, awaiting admin verification
    Payment::create([
        'booking_id' => $booking->id,
        'fine_id' => $fine->id,
        'amount' => 50000,
        'payment_method' => 'bank_transfer',
        'status' => PaymentStatus::Pending,
        'payment_proof' => 'proofs/fine_proof.jpg',
    ]);

    expect($booking->fresh()->hasPendingFinePayments())->toBeTrue();

    // User attempts to review
    $response = $this->actingAs($customer)->post("/booking-items/{$item->id}/review", [
        'rating' => 5,
        'comment' => 'Ulasan denda pending',
    ]);

    $response->assertForbidden();
    expect(Review::where('booking_items_id', $item->id)->exists())->toBeFalse();
});

test('user can submit review only after motor is returned, fines settled, and booking is completed', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotorItem();
    $booking = createRoleTestBooking($customer, $motor, BookingStatus::Confirmed->value);
    $item = $booking->items->first();

    // Motor returned on time
    app(ReturnConfirmationService::class)->confirmReturn($item, Carbon::parse('2026-10-03 10:00:00'));

    // Admin completes booking
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    app(BookingCompletionService::class)->completeBookingByAdmin($booking, $admin);

    expect($booking->fresh()->status)->toBe(BookingStatus::Completed);

    // User submits review
    $response = $this->actingAs($customer)->post("/booking-items/{$item->id}/review", [
        'rating' => 5,
        'comment' => 'Motor sangat terawat dan pelayanan mantap!',
    ]);

    $response->assertRedirect();
    expect(Review::where('booking_items_id', $item->id)->first()->comment)->toBe('Motor sangat terawat dan pelayanan mantap!');
});

/*
|--------------------------------------------------------------------------
| FEEDBACK 2: ADMIN - TOPBAR "HOME" REDIRECTS TO ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

test('admin topbar home link points to admin dashboard and not customer homepage', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();

    // Topbar Home must point to admin.dashboard
    $adminDashboardUrl = route('admin.dashboard');
    $customerHomeUrl = route('home');

    // Desktop nav-links: Home should link to admin dashboard
    $response->assertSee('<a href="' . $adminDashboardUrl . '" class="active">Home</a>', false);

    // Should NOT have Home link pointing to customer home in nav-links
    $response->assertDontSee('<a href="' . $customerHomeUrl . '" class="active">Home</a>', false);
    $response->assertDontSee('<a href="' . $customerHomeUrl . '" class="">Home</a>', false);
});

test('admin on motors page sees topbar home pointing to admin dashboard without active class', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.motors.index'));

    $response->assertOk();

    $adminDashboardUrl = route('admin.dashboard');
    // Topbar Home must still link to admin dashboard
    $response->assertSee('<a href="' . $adminDashboardUrl . '" class="">Home</a>', false);
    // Daftar Kendaraan must be active
    $response->assertSee('<a href="' . route('admin.motors.index') . '" class="active">Daftar Kendaraan</a>', false);
});

test('customer topbar home link points to customer homepage', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $response = $this->actingAs($customer)->get(route('home'));

    $response->assertOk();

    $customerHomeUrl = route('home');
    $adminDashboardUrl = route('admin.dashboard');

    // Customer Home link points to customer home and is active
    $response->assertSee('<a href="' . $customerHomeUrl . '" class="active">Home</a>', false);
    // Must NOT link to admin dashboard
    $response->assertDontSee('<a href="' . $adminDashboardUrl, false);
});

test('guest topbar home link points to customer homepage', function () {
    $response = $this->get(route('home'));

    $response->assertOk();

    $customerHomeUrl = route('home');
    $adminDashboardUrl = route('admin.dashboard');

    $response->assertSee('<a href="' . $customerHomeUrl . '" class="active">Home</a>', false);
    $response->assertDontSee('<a href="' . $adminDashboardUrl, false);
});
