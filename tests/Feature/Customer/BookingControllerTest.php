<?php

use App\Enums\BookingStatus;
use App\Enums\MotorStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('shows booking details in the requested order', function () {
    $user = User::factory()->create();
    $category = MotorCategory::create([
        'name' => 'Matic',
        'slug' => 'matic',
    ]);
    $motor = Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Yamaha',
        'model' => 'NMAX',
        'warna' => 'Hitam',
        'year' => 2024,
        'plate_number' => 'AB 5678 CD',
        'price_per_day' => 100000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);

    $this->actingAs($user)
        ->get(route('customer.bookings.create', $motor))
        ->assertOk()
        ->assertSeeInOrder([
            '1. Motor yang dipesan',
            '2. Tanggal Sewa',
            '3. Metode Pengambilan',
            '4. Catatan Tambahan',
            '5. Ringkasan Biaya',
            '6. Konfirmasi Pemesanan',
        ]);
});

it('creates a booking with a payment deadline', function () {
    $this->travelTo('2026-10-31 07:03:23');

    $user = User::factory()->create();
    $category = MotorCategory::create([
        'name' => 'Matic',
        'slug' => 'matic',
    ]);
    $motor = Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Yamaha',
        'model' => 'NMAX',
        'warna' => 'Hitam',
        'year' => 2024,
        'plate_number' => 'AB 5678 CD',
        'price_per_day' => 80000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);

    $this->actingAs($user)
        ->post(route('customer.bookings.store', $motor), [
            'start_date' => '2026-10-31',
            'end_date' => '2026-11-01',
            'pickup_method' => 'pickup',
            'notes' => 'Jaga motor dengan baik.',
        ])
        ->assertRedirect(route('customer.bookings.index'));

    $booking = Booking::query()->where('user_id', $user->id)->firstOrFail();

    expect($booking->payment_deadline?->toDateTimeString())
        ->toBe('2026-11-01 07:03:23');
});

it('hides the pay button after a customer submits payment proof', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $booking = Booking::create([
        'user_id' => $user->id,
        'start_date' => today(),
        'end_date' => today()->addDay(),
        'pickup_method' => 'pickup',
        'subtotal' => 100000,
        'delivery_fee' => 0,
        'discount' => 0,
        'total_price' => 100000,
        'status' => BookingStatus::Pending,
    ]);
    Payment::create([
        'booking_id' => $booking->id,
        'amount' => 100000,
        'status' => PaymentStatus::Pending,
    ]);

    $this->actingAs($user)
        ->get(route('customer.bookings.show', $booking))
        ->assertOk()
        ->assertSee('Bayar Sekarang');

    $this->actingAs($user)
        ->post(route('customer.payments.upload', $booking), [
            'payment_method' => 'BCA',
            'payment_proof' => UploadedFile::fake()->create('proof.jpg', 10, 'image/jpeg'),
        ])
        ->assertRedirect(route('customer.bookings.show', $booking));

    $payment = $booking->payment()->firstOrFail();
    Storage::disk('public')->assertExists($payment->payment_proof);

    $this->get(route('customer.bookings.show', $booking))
        ->assertOk()
        ->assertDontSee('Bayar Sekarang');

    $payment->update([
        'payment_proof' => null,
        'status' => PaymentStatus::Paid,
    ]);

    $this->get(route('customer.bookings.show', $booking))
        ->assertOk()
        ->assertDontSee('Bayar Sekarang');
});
