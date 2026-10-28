<?php

use App\Enums\BookingStatus;
use App\Enums\MotorStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters admin bookings by vehicle type, date, and search term', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['name' => 'Dina Rahma']);
    $matic = MotorCategory::create(['name' => 'Matic', 'slug' => 'matic']);
    $manual = MotorCategory::create(['name' => 'Manual', 'slug' => 'manual']);
    $matchingMotor = Motor::create([
        'motor_category_id' => $matic->id,
        'merk' => 'Yamaha',
        'model' => 'NMAX',
        'warna' => 'Hitam',
        'year' => 2025,
        'plate_number' => 'AB 1234 CD',
        'price_per_day' => 100000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);
    $otherMotor = Motor::create([
        'motor_category_id' => $manual->id,
        'merk' => 'Honda',
        'model' => 'CBR',
        'warna' => 'Merah',
        'year' => 2025,
        'plate_number' => 'AB 4321 CD',
        'price_per_day' => 125000,
        'deposit' => 350000,
        'status' => MotorStatus::Available,
    ]);
    $matchingBooking = Booking::create([
        'user_id' => $customer->id,
        'start_date' => '2026-11-12',
        'end_date' => '2026-11-14',
        'pickup_method' => 'pickup',
        'subtotal' => 200000,
        'delivery_fee' => 0,
        'discount' => 0,
        'total_price' => 200000,
        'status' => BookingStatus::Pending,
    ]);
    $matchingBooking->items()->create([
        'motor_id' => $matchingMotor->id,
        'price_per_day' => 100000,
        'due_date' => '2026-11-14',
        'total_days' => 2,
        'subtotal' => 200000,
        'deposit' => 300000,
    ]);
    $otherBooking = Booking::create([
        'user_id' => $customer->id,
        'start_date' => '2026-11-12',
        'end_date' => '2026-11-14',
        'pickup_method' => 'pickup',
        'subtotal' => 250000,
        'delivery_fee' => 0,
        'discount' => 0,
        'total_price' => 250000,
        'status' => BookingStatus::Pending,
    ]);
    $otherBooking->items()->create([
        'motor_id' => $otherMotor->id,
        'price_per_day' => 125000,
        'due_date' => '2026-11-14',
        'total_days' => 2,
        'subtotal' => 250000,
        'deposit' => 350000,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.bookings.index', [
        'category' => $matic->id,
        'start_date' => '2026-11-12',
        'search' => 'Dina',
    ]));

    $response->assertOk()
        ->assertSee($matchingBooking->booking_code)
        ->assertDontSee($otherBooking->booking_code)
        ->assertSee('Tipe kendaraan')
        ->assertSee('name="start_date"', false);
});
