<?php

use App\Enums\BookingStatus;
use App\Enums\FineStatus;
use App\Enums\FineType;
use App\Enums\MotorStatus;
use App\Enums\ReturnStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Fine;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\Review;
use App\Models\User;
use App\Services\BookingCompletionService;
use App\Services\FineCalculationService;
use App\Services\FinePaymentService;
use App\Services\ReturnConfirmationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createTestMotor(float $pricePerDay = 100000): Motor
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
        'plate_number' => 'AB '.rand(1000, 9999).' XY',
        'price_per_day' => $pricePerDay,
        'deposit' => 200000,
        'status' => MotorStatus::Available,
    ]);
}

function createTestBooking(User $user, Motor $motor, string $status = 'pending', ?Carbon $dueDate = null, float $pricePerDay = 100000): Booking
{
    $start = Carbon::parse('2026-10-01 10:00:00');
    $end = $dueDate ?? Carbon::parse('2026-10-03 10:00:00');

    $booking = Booking::create([
        'user_id' => $user->id,
        'booking_code' => 'BK-'.strtoupper(uniqid()),
        'start_date' => $start,
        'end_date' => $end,
        'pickup_method' => 'pickup',
        'subtotal' => $pricePerDay * 2,
        'delivery_fee' => 0,
        'discount' => 0,
        'total_price' => $pricePerDay * 2,
        'status' => $status,
    ]);

    $booking->items()->create([
        'motor_id' => $motor->id,
        'price_per_day' => $pricePerDay,
        'due_date' => $end,
        'total_days' => 2,
        'subtotal' => $pricePerDay * 2,
        'deposit' => 200000,
        'return_status' => ReturnStatus::NotReturned,
    ]);

    return $booking->fresh(['items.motor']);
}

// 1. Booking pending tidak menghasilkan denda
test('1. booking pending tidak menghasilkan denda', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $pastDue = now()->subDays(3);
    $booking = createTestBooking($customer, $motor, BookingStatus::Pending->value, $pastDue);

    $service = app(FineCalculationService::class);
    $fine = $service->calculateForItem($booking->items->first(), now());

    expect($fine)->toBeNull();
    expect(Fine::count())->toBe(0);
});

// 2. Booking rejected atau cancelled tidak menghasilkan denda keterlambatan
test('2. booking rejected atau cancelled tidak menghasilkan denda keterlambatan', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $pastDue = now()->subDays(2);

    $rejectedBooking = createTestBooking($customer, $motor, BookingStatus::Rejected->value, $pastDue);
    $cancelledBooking = createTestBooking($customer, $motor, BookingStatus::Cancelled->value, $pastDue);

    $service = app(FineCalculationService::class);
    expect($service->calculateForItem($rejectedBooking->items->first(), now()))->toBeNull();
    expect($service->calculateForItem($cancelledBooking->items->first(), now()))->toBeNull();
    expect(Fine::count())->toBe(0);
});

// 3. Booking confirmed yang belum terlambat tidak menghasilkan denda keterlambatan
test('3. booking confirmed yang belum terlambat tidak menghasilkan denda keterlambatan', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $futureDue = now()->addDays(2);
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $futureDue);

    $service = app(FineCalculationService::class);
    $fine = $service->calculateForItem($booking->items->first(), now());

    expect($fine)->toBeNull();
    expect(Fine::count())->toBe(0);
});

// 4. Booking terlambat menghasilkan denda sesuai tarif
test('4. booking terlambat menghasilkan denda sesuai tarif (harga sewa + 30000 per hari)', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    // 2 days late: due 2 days ago
    $dueDate = Carbon::parse('2026-10-20 10:00:00');
    $checkTime = Carbon::parse('2026-10-22 10:00:00'); // exactly 48 hours = 2 days late

    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate, 100000);

    $service = app(FineCalculationService::class);
    $fine = $service->calculateForItem($booking->items->first(), $checkTime);

    expect($fine)->not->toBeNull();
    expect($fine->type)->toBe(FineType::Late);
    expect($fine->days_late)->toBe(2);
    // Formula: 2 * (100000 + 30000) = 260000
    expect((float) $fine->amount)->toBe(260000.0);
    expect($fine->status)->toBe(FineStatus::Unpaid);
});

// 5. Scheduler dijalankan berulang kali tanpa membuat denda ganda
test('5. scheduler dijalankan berulang kali tanpa membuat denda ganda (idempotent)', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $dueDate = now()->subDays(2);
    createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate, 100000);

    $service = app(FineCalculationService::class);
    $service->calculateAllActiveBookings();
    $service->calculateAllActiveBookings();
    $service->calculateAllActiveBookings();

    expect(Fine::where('type', FineType::Late)->count())->toBe(1);
});

// 6. Nominal denda meningkat sesuai jumlah hari keterlambatan
test('6. nominal denda meningkat sesuai jumlah hari keterlambatan saat belum dikembalikan', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $dueDate = Carbon::parse('2026-10-20 10:00:00');
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate, 100000);
    $item = $booking->items->first();

    $service = app(FineCalculationService::class);

    // Day 1 check
    $day1 = Carbon::parse('2026-10-21 10:00:00');
    $fine1 = $service->calculateForItem($item, $day1);
    expect($fine1->days_late)->toBe(1);
    expect((float) $fine1->amount)->toBe(130000.0);

    // Day 3 check
    $day3 = Carbon::parse('2026-10-23 10:00:00');
    $fine3 = $service->calculateForItem($item, $day3);
    expect($fine3->days_late)->toBe(3);
    // 3 * (100000 + 30000) = 390000
    expect((float) $fine3->amount)->toBe(390000.0);
    expect(Fine::count())->toBe(1);
});

// 7. Konfirmasi pengembalian menghentikan kenaikan denda
test('7. konfirmasi pengembalian menghentikan kenaikan denda', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $dueDate = Carbon::parse('2026-10-20 10:00:00');
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate, 100000);
    $item = $booking->items->first();

    // Motor returned 2 days late
    $returnTime = Carbon::parse('2026-10-22 10:00:00');
    $returnService = app(ReturnConfirmationService::class);
    $result = $returnService->confirmReturn($item, $returnTime);

    expect($result['success'])->toBeTrue();
    expect($item->fresh()->isReturned())->toBeTrue();
    expect($item->fresh()->returned_at)->not->toBeNull();

    $frozenFine = Fine::where('booking_item_id', $item->id)->where('type', FineType::Late)->first();
    expect($frozenFine)->not->toBeNull();
    expect((float) $frozenFine->amount)->toBe(260000.0);

    // A week later, run fine calculation again
    $calcService = app(FineCalculationService::class);
    $calcService->calculateForItem($item->fresh(), Carbon::parse('2026-10-30 10:00:00'));

    // Amount remains frozen at 260,000!
    expect((float) $frozenFine->fresh()->amount)->toBe(260000.0);
});

// 8. Denda kerusakan dapat dibuat oleh Admin dengan validasi yang benar
test('8. denda kerusakan dapat dibuat oleh admin dengan validasi yang benar', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value);
    $item = $booking->items->first();
    $item->update([
        'return_status' => ReturnStatus::Returned,
        'returned_at' => now(),
    ]);

    $response = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $item->id,
        'type' => 'damage',
        'description' => 'Lampu sein kanan pecah',
        'amount' => 150000,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('fines', [
        'booking_item_id' => $item->id,
        'type' => 'damage',
        'description' => 'Lampu sein kanan pecah',
        'amount' => 150000,
        'status' => 'unpaid',
    ]);
});

// 9. User tidak dapat membuat atau mengubah denda Admin
test('9. user customer tidak dapat membuat atau mengubah denda admin', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value);
    $item = $booking->items->first();

    // Customer attempts to post to /admin/fines
    $response = $this->actingAs($customer)->post('/admin/fines', [
        'booking_item_id' => $item->id,
        'type' => 'damage',
        'description' => 'Ilegal fine',
        'amount' => 50000,
    ]);

    $response->assertForbidden();
    expect(Fine::count())->toBe(0);
});

// 10. Denda unpaid mencegah booking menjadi Completed
test('10. denda unpaid mencegah booking menjadi Completed', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $dueDate = Carbon::parse('2026-10-20 10:00:00');
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate);
    $item = $booking->items->first();

    // Confirm return with 2 days late
    $returnTime = Carbon::parse('2026-10-22 10:00:00');
    $returnService = app(ReturnConfirmationService::class);
    $result = $returnService->confirmReturn($item, $returnTime);

    // Booking has unpaid late fine
    expect($result['booking_completed'])->toBeFalse();
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
    expect($booking->fresh()->hasUnpaidFines())->toBeTrue();

    // Direct completion service check also fails
    $completionService = app(BookingCompletionService::class);
    expect($completionService->checkAndComplete($booking->fresh()))->toBeFalse();
});

// 11. Pembayaran berhasil memperbarui status denda menjadi paid
test('11. pembayaran berhasil memperbarui status denda menjadi paid', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value);
    $item = $booking->items->first();

    $fine = Fine::create([
        'booking_item_id' => $item->id,
        'type' => FineType::Damage,
        'description' => 'Goresan bodi samping',
        'amount' => 75000,
        'status' => FineStatus::Unpaid,
    ]);

    $response = $this->actingAs($customer)->post("/fines/{$fine->id}/pay", [
        'payment_method' => 'bank_transfer',
    ]);

    $response->assertRedirect();
    $fine->refresh();
    expect($fine->status)->toBe(FineStatus::Paid);
    expect($fine->paid_at)->not->toBeNull();
    expect($fine->payment_method)->toBe('bank_transfer');
    expect($fine->transaction_reference)->not->toBeNull();
});

// 12. Callback pembayaran berulang tidak menyebabkan pembayaran ganda
test('12. callback / pembayaran berulang tidak menyebabkan pembayaran ganda', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value);
    $item = $booking->items->first();

    $fine = Fine::create([
        'booking_item_id' => $item->id,
        'type' => FineType::Damage,
        'description' => 'Helm hilang',
        'amount' => 100000,
        'status' => FineStatus::Unpaid,
    ]);

    $paymentService = app(FinePaymentService::class);
    $result1 = $paymentService->payFine($fine, $customer, 'qris');
    expect($result1['success'])->toBeTrue();

    // Attempt second payment
    expect(fn () => $paymentService->payFine($fine->fresh(), $customer, 'qris'))
        ->toThrow(InvalidArgumentException::class, 'Denda ini sudah dibayar sebelumnya.');
});

// 13. Booking menjadi Completed setelah pengembalian dan seluruh denda selesai
test('13. booking menjadi Completed setelah pengembalian dan seluruh denda selesai', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $dueDate = Carbon::parse('2026-10-20 10:00:00');
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate);
    $item = $booking->items->first();

    // Return motor with 1 day late
    $returnService = app(ReturnConfirmationService::class);
    $returnService->confirmReturn($item, Carbon::parse('2026-10-21 10:00:00'));

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);

    // Pay the late fine
    $lateFine = Fine::where('booking_item_id', $item->id)->first();
    $paymentService = app(FinePaymentService::class);
    $payResult = $paymentService->payFine($lateFine, $customer, 'bank_transfer');

    // Status booking tetap Confirmed (Menunggu Konfirmasi Pengembalian)
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
    expect($booking->fresh()->isEligibleForCompletion())->toBeTrue();

    // Admin menyelesaikan booking secara eksplisit
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $completeResult = app(BookingCompletionService::class)->completeBookingByAdmin($booking, $admin);
    expect($completeResult['success'])->toBeTrue();
    expect($booking->fresh()->status)->toBe(BookingStatus::Completed);
});

// 14. Review ditolak jika masih ada denda unpaid
test('14. review ditolak jika masih ada denda unpaid atau belum kembali', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $dueDate = Carbon::parse('2026-10-20 10:00:00');
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate);
    $item = $booking->items->first();

    // Attempt review before returning motor
    $resp1 = $this->actingAs($customer)->post("/booking-items/{$item->id}/review", [
        'rating' => 5,
        'comment' => 'Motor mantap',
    ]);
    $resp1->assertForbidden();

    // Return motor (creates unpaid late fine)
    app(ReturnConfirmationService::class)->confirmReturn($item, Carbon::parse('2026-10-22 10:00:00'));

    // Attempt review with unpaid fine
    $resp2 = $this->actingAs($customer)->post("/booking-items/{$item->id}/review", [
        'rating' => 5,
        'comment' => 'Motor mantap',
    ]);
    $resp2->assertForbidden();
});

// 15. Review valid dapat disimpan setelah seluruh persyaratan selesai
test('15. review valid dapat disimpan setelah seluruh persyaratan selesai', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, now()->addDays(2));
    $item = $booking->items->first();

    // Returned on time (motor returns, then admin completes booking)
    app(ReturnConfirmationService::class)->confirmReturn($item, now());
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    app(BookingCompletionService::class)->completeBookingByAdmin($booking, $admin);
    expect($booking->fresh()->status)->toBe(BookingStatus::Completed);

    $response = $this->actingAs($customer)->post("/booking-items/{$item->id}/review", [
        'rating' => 5,
        'comment' => 'Pelayanan ramah dan motor terawat sangat baik!',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('reviews', [
        'booking_items_id' => $item->id,
        'rating' => 5,
        'comment' => 'Pelayanan ramah dan motor terawat sangat baik!',
    ]);

    // Second review on same item must be rejected
    $duplicate = $this->actingAs($customer)->post("/booking-items/{$item->id}/review", [
        'rating' => 4,
        'comment' => 'Duplikat review',
    ]);
    $duplicate->assertForbidden();
});

// 16. User tidak dapat melihat booking atau denda milik user lain
test('16. user tidak dapat melihat booking atau denda milik user lain', function () {
    $userA = User::factory()->create(['role' => UserRole::Customer]);
    $userB = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);

    $bookingA = createTestBooking($userA, $motor, BookingStatus::Confirmed->value);
    $itemA = $bookingA->items->first();
    $fineA = Fine::create([
        'booking_item_id' => $itemA->id,
        'type' => FineType::Damage,
        'description' => 'Kerusakan user A',
        'amount' => 50000,
        'status' => FineStatus::Unpaid,
    ]);

    // User B tries to view booking A
    $respBooking = $this->actingAs($userB)->get("/bookings/{$bookingA->id}");
    $respBooking->assertForbidden();

    // User B tries to pay fine of user A
    $respPay = $this->actingAs($userB)->post("/fines/{$fineA->id}/pay", [
        'payment_method' => 'bank_transfer',
    ]);
    $respPay->assertSessionHas('error');
    expect($fineA->fresh()->status)->toBe(FineStatus::Unpaid);
});

// 17. Harga sewa pada booking lama tetap konsisten meskipun harga motor berubah
test('17. harga sewa pada booking lama tetap konsisten meskipun harga motor berubah', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $dueDate = Carbon::parse('2026-10-20 10:00:00');
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate, 100000);
    $item = $booking->items->first();

    // Update motor catalog price to Rp 250.000 / day
    $motor->update(['price_per_day' => 250000]);

    // Late calculation on booking item must use frozen 100.000, NOT 250.000
    $calcService = app(FineCalculationService::class);
    $fine = $calcService->calculateForItem($item, Carbon::parse('2026-10-21 10:00:00'));

    expect($fine)->not->toBeNull();
    // 1 day * (100000 frozen + 30000) = 130000
    expect((float) $fine->amount)->toBe(130000.0);
    expect((float) $fine->amount)->not->toBe(280000.0);
});

// 18. Sistem menangani pengembalian lebih awal
test('18. sistem menangani pengembalian lebih awal tanpa denda keterlambatan', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $dueDate = Carbon::parse('2026-10-25 18:00:00');
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate);
    $item = $booking->items->first();

    // Early return: 2 days before due date
    $earlyReturnTime = Carbon::parse('2026-10-23 12:00:00');
    $returnService = app(ReturnConfirmationService::class);
    $result = $returnService->confirmReturn($item, $earlyReturnTime);

    expect($result['success'])->toBeTrue();
    expect($result['days_late'])->toBe(0);
    expect($result['late_fine'])->toBeNull();
    expect($result['booking_completed'])->toBeFalse();
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
    expect($booking->fresh()->isEligibleForCompletion())->toBeTrue();

    // Admin explicitly completes the booking
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $completeResult = app(BookingCompletionService::class)->completeBookingByAdmin($booking, $admin);
    expect($completeResult['success'])->toBeTrue();
    expect($booking->fresh()->status)->toBe(BookingStatus::Completed);
    expect(Fine::count())->toBe(0);
});

// 19. Sistem menangani keterlambatan lintas tengah malam dan pergantian tanggal
test('19. sistem menangani keterlambatan lintas tengah malam dan pergantian tanggal', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    // Due at 23:59:00 on October 20
    $dueDate = Carbon::parse('2026-10-20 23:59:00');
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate, 100000);
    $item = $booking->items->first();

    $calcService = app(FineCalculationService::class);

    // Crosses midnight to 00:05:00 on October 21 (6 minutes past due)
    $midnightCross = Carbon::parse('2026-10-21 00:05:00');
    $fine = $calcService->calculateForItem($item, $midnightCross);

    expect($fine)->not->toBeNull();
    expect($fine->days_late)->toBe(1);
    expect((float) $fine->amount)->toBe(130000.0);

    // 25 hours later (into October 22 01:00:00) = 2 days late
    $nextDay = Carbon::parse('2026-10-22 01:00:00');
    $fine2 = $calcService->calculateForItem($item, $nextDay);

    expect($fine2->days_late)->toBe(2);
    expect((float) $fine2->amount)->toBe(260000.0);
});

// 20. Sistem menangani dua proses scheduler / calculation yang berjalan bersamaan
test('20. sistem menangani dua proses scheduler atau perhitungan yang berjalan bersamaan', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $motor = createTestMotor(100000);
    $dueDate = Carbon::parse('2026-10-20 10:00:00');
    $booking = createTestBooking($customer, $motor, BookingStatus::Confirmed->value, $dueDate, 100000);
    $item = $booking->items->first();

    $checkTime = Carbon::parse('2026-10-22 10:00:00');
    $service1 = app(FineCalculationService::class);
    $service2 = app(FineCalculationService::class);

    // Run concurrently / consecutively
    $fine1 = $service1->calculateForItem($item, $checkTime);
    $fine2 = $service2->calculateForItem($item, $checkTime);

    expect(Fine::where('booking_item_id', $item->id)->where('type', FineType::Late)->count())->toBe(1);
    expect($fine1->id)->toBe($fine2->id);
    expect((float) $fine2->amount)->toBe(260000.0);
});
