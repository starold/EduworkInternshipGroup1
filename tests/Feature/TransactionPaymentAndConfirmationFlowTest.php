<?php

use App\Enums\BookingStatus;
use App\Enums\MotorStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReturnStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function createFlowMotor(): Motor
{
    $category = MotorCategory::firstOrCreate(
        ['slug' => 'matic'],
        ['name' => 'Matic', 'description' => 'Motor Matic']
    );

    return Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Honda',
        'model' => 'PCX 160',
        'warna' => 'Putih',
        'year' => 2024,
        'plate_number' => 'AB '.rand(1000, 9999).' PC',
        'price_per_day' => 120000,
        'deposit' => 200000,
        'status' => MotorStatus::Available,
    ]);
}

function createFlowBooking(User $user, Motor $motor, string $pickupMethod = 'delivery'): Booking
{
    $start = Carbon::parse('2026-11-01');
    $end = Carbon::parse('2026-11-03');

    $booking = Booking::create([
        'user_id' => $user->id,
        'booking_code' => 'BK-'.strtoupper(uniqid()),
        'start_date' => $start,
        'end_date' => $end,
        'pickup_method' => $pickupMethod,
        'delivery_address' => $pickupMethod === 'delivery' ? 'Jl. Kaliurang KM 5, Sleman' : null,
        'subtotal' => 240000,
        'delivery_fee' => $pickupMethod === 'delivery' ? 15000 : 0,
        'discount' => 0,
        'total_price' => $pickupMethod === 'delivery' ? 255000 : 240000,
        'status' => BookingStatus::Pending,
    ]);

    $booking->items()->create([
        'motor_id' => $motor->id,
        'price_per_day' => 120000,
        'due_date' => $end,
        'total_days' => 2,
        'subtotal' => 240000,
        'deposit' => 200000,
        'return_status' => ReturnStatus::NotReturned,
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'amount' => $booking->total_price,
        'status' => PaymentStatus::Pending,
    ]);

    return $booking->fresh(['items.motor.category', 'payment']);
}

// 1. Pending booking without paid payment has uiStatusLabel 'Menunggu Pembayaran'
test('1. pending booking without paid payment has uiStatusLabel Menunggu Pembayaran', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    expect($booking->uiStatusLabel())->toBe('Menunggu Pembayaran');
    expect($booking->isWaitingPayment())->toBeTrue();
    expect($booking->isWaitingConfirmation())->toBeFalse();

    $response = $this->actingAs($customer)->get(route('customer.bookings.show', $booking));
    $response->assertOk();
    $response->assertSee('Menunggu Pembayaran');
});

// 2. User can upload payment proof during 'Menunggu Pembayaran'
test('2. user can upload payment proof during Menunggu Pembayaran', function () {
    Storage::fake('public');
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    $file = UploadedFile::fake()->create('bukti_transfer.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($customer)->post(route('customer.payments.upload', $booking), [
        'payment_method' => 'BCA',
        'payment_proof' => $file,
    ]);

    $response->assertRedirect(route('customer.bookings.show', $booking));
    $booking->refresh();

    expect($booking->payment->status)->toBe(PaymentStatus::Pending);
    expect($booking->payment->payment_proof)->not->toBeNull();
    expect($booking->uiStatusLabel())->toBe('Menunggu Pembayaran');
});

// 3. Admin cannot confirm booking when payment is not yet completed
test('3. admin cannot confirm booking when payment is not yet completed', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    // Payment is still pending (not paid)
    $response = $this->actingAs($admin)->post(route('admin.bookings.confirm', $booking));

    $response->assertStatus(422);
    expect($booking->fresh()->status)->toBe(BookingStatus::Pending);
});

// 4. Admin cannot reject booking when payment is not yet completed
test('4. admin cannot reject booking when payment is not yet completed', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    $response = $this->actingAs($admin)->post(route('admin.bookings.reject', $booking), [
        'reason' => 'Ingin menolak sebelum bayar',
    ]);

    $response->assertStatus(422);
    expect($booking->fresh()->status)->toBe(BookingStatus::Pending);
});

// 5. Admin can reject payment with reason during Menunggu Pembayaran
test('5. admin can reject payment with reason during Menunggu Pembayaran', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    $booking->payment->update([
        'payment_proof' => 'payment-proofs/test.jpg',
        'status' => PaymentStatus::Pending,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.bookings.payments.reject', $booking), [
        'rejection_reason' => 'Nominal transfer tidak sesuai dengan tagihan.',
    ]);

    $response->assertRedirect();
    $payment = $booking->fresh()->payment;
    expect($payment->status)->toBe(PaymentStatus::Failed);
    expect($payment->rejection_reason)->toBe('Nominal transfer tidak sesuai dengan tagihan.');

    // Customer visits page and sees rejection reason
    $custPage = $this->actingAs($customer)->get(route('customer.bookings.show', $booking));
    $custPage->assertSee('Nominal transfer tidak sesuai dengan tagihan.');
    $custPage->assertSee('Upload Ulang Bukti Bayar');
});

// 6. User can re-upload payment proof after payment was rejected
test('6. user can re-upload payment proof after payment was rejected', function () {
    Storage::fake('public');
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    $booking->payment->update([
        'status' => PaymentStatus::Failed,
        'rejection_reason' => 'Bukti buram',
    ]);

    $newFile = UploadedFile::fake()->create('bukti_baru.png', 100, 'image/png');

    $response = $this->actingAs($customer)->post(route('customer.payments.upload', $booking), [
        'payment_method' => 'Mandiri',
        'payment_proof' => $newFile,
    ]);

    $response->assertRedirect(route('customer.bookings.show', $booking));
    $payment = $booking->fresh()->payment;
    expect($payment->status)->toBe(PaymentStatus::Pending);
    expect($payment->rejection_reason)->toBeNull();
});

// 7. Admin can verify payment as Paid (booking uiStatusLabel becomes Menunggu Konfirmasi)
test('7. admin can verify payment as Paid and status becomes Menunggu Konfirmasi', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    $booking->payment->update([
        'payment_proof' => 'payment-proofs/test.jpg',
        'status' => PaymentStatus::Pending,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.bookings.payments.verify', $booking));
    $response->assertRedirect();

    $booking->refresh();
    expect($booking->payment->status)->toBe(PaymentStatus::Paid);
    expect($booking->payment->paid_at)->not->toBeNull();
    expect($booking->uiStatusLabel())->toBe('Menunggu Konfirmasi');
    expect($booking->isWaitingConfirmation())->toBeTrue();
    expect($booking->isWaitingPayment())->toBeFalse();
});

// 8. User cannot upload or access payment page during Menunggu Konfirmasi
test('8. user cannot upload or access payment page during Menunggu Konfirmasi', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    $booking->payment->update([
        'payment_proof' => 'payment-proofs/test.jpg',
        'status' => PaymentStatus::Paid,
        'paid_at' => now(),
    ]);

    // Access payment show page must redirect
    $response = $this->actingAs($customer)->get(route('customer.payments.show', $booking));
    $response->assertRedirect(route('customer.bookings.show', $booking));

    // Upload attempt must be forbidden (403)
    $upload = $this->actingAs($customer)->post(route('customer.payments.upload', $booking), [
        'payment_method' => 'BCA',
        'payment_proof' => UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg'),
    ]);
    $upload->assertForbidden();

    // Booking show page must NOT show 'Bayar Sekarang'
    $showPage = $this->actingAs($customer)->get(route('customer.bookings.show', $booking));
    $showPage->assertDontSee('Bayar Sekarang');
    $showPage->assertSee('Menunggu Konfirmasi');
});

// 9. Admin can confirm booking during Menunggu Konfirmasi and status becomes Sedang Disewa
test('9. admin can confirm booking during Menunggu Konfirmasi and status becomes Sedang Disewa', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    $booking->payment->update([
        'status' => PaymentStatus::Paid,
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($admin)->post(route('admin.bookings.confirm', $booking));
    $response->assertRedirect();

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Confirmed);
    expect($booking->uiStatusLabel())->toBe('Sedang Disewa');
    expect($booking->items->first()->rental)->not->toBeNull();
});

// 10. Confirmed delivery booking displays delivery instructions
test('10. confirmed delivery booking displays delivery instructions', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor, 'delivery');

    $booking->update(['status' => BookingStatus::Confirmed]);
    $booking->payment->update(['status' => PaymentStatus::Paid]);

    expect($booking->getDeliveryInstructions())->toBe('Silahkan tunggu motor anda datang. (maksimal 1x24 jam)');

    $response = $this->actingAs($customer)->get(route('customer.bookings.show', $booking));
    $response->assertOk();
    $response->assertSee('Silahkan tunggu motor anda datang. (maksimal 1x24 jam)');
});

// 11. Confirmed pickup booking displays pickup counter instructions
test('11. confirmed pickup booking displays pickup counter instructions', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor, 'pickup');

    $booking->update(['status' => BookingStatus::Confirmed]);
    $booking->payment->update(['status' => PaymentStatus::Paid]);

    expect($booking->getDeliveryInstructions())->toBe('Silahkan datang ke konter langsung.');

    $response = $this->actingAs($customer)->get(route('customer.bookings.show', $booking));
    $response->assertOk();
    $response->assertSee('Silahkan datang ke konter langsung.');
});

// 12. Admin can reject booking during Menunggu Konfirmasi
test('12. admin can reject booking during Menunggu Konfirmasi', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    $booking->payment->update([
        'status' => PaymentStatus::Paid,
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($admin)->post(route('admin.bookings.reject', $booking), [
        'reason' => 'Unit motor sedang dalam perawatan mendadak',
    ]);

    $response->assertRedirect();
    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Rejected);
    expect($booking->uiStatusLabel())->toBe('Ditolak');
    expect($booking->notes)->toBe('Unit motor sedang dalam perawatan mendadak');
});

// 13. Payment upload is rejected after deadline (expired)
test('13. payment upload is rejected after deadline', function () {
    Storage::fake('public');
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createFlowMotor();
    $booking = createFlowBooking($customer, $motor);

    // Simulate booking created 25 hours ago
    $booking->created_at = now()->subHours(25);
    $booking->save();

    expect($booking->isPaymentExpired())->toBeTrue();

    $response = $this->actingAs($customer)->post(route('customer.payments.upload', $booking), [
        'payment_method' => 'BCA',
        'payment_proof' => UploadedFile::fake()->create('bukti_telat.jpg', 100, 'image/jpeg'),
    ]);

    $response->assertSessionHasErrors('payment_proof');
});
