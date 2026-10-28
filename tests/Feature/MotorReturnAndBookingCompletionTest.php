<?php

use App\Enums\BookingStatus;
use App\Enums\FineStatus;
use App\Enums\FineType;
use App\Enums\MotorStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReturnStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Fine;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\Payment;
use App\Models\User;
use App\Services\BookingCompletionService;
use App\Services\FinePaymentService;
use App\Services\ReturnConfirmationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createReturnTestContext(bool $isReturned = false): array
{
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $category = MotorCategory::firstOrCreate(
        ['slug' => 'matic'],
        ['name' => 'Matic', 'description' => 'Motor Matic']
    );

    $motor = Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Honda',
        'model' => 'Scoopy',
        'warna' => 'Merah',
        'year' => 2024,
        'plate_number' => 'AB ' . rand(1000, 9999) . ' XX',
        'price_per_day' => 100000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);

    $booking = Booking::create([
        'user_id' => $customer->id,
        'start_date' => Carbon::parse('2026-10-10'),
        'end_date' => Carbon::parse('2026-10-12'),
        'pickup_method' => 'pickup',
        'subtotal' => 200000,
        'delivery_fee' => 0,
        'discount' => 0,
        'total_price' => 200000,
        'status' => BookingStatus::Confirmed,
    ]);

    $item = $booking->items()->create([
        'motor_id' => $motor->id,
        'price_per_day' => 100000,
        'due_date' => Carbon::parse('2026-10-12 10:00:00'),
        'total_days' => 2,
        'subtotal' => 200000,
        'deposit' => 300000,
        'return_status' => $isReturned ? ReturnStatus::Returned : ReturnStatus::NotReturned,
        'returned_at' => $isReturned ? Carbon::parse('2026-10-12 09:00:00') : null,
    ]);

    return compact('admin', 'customer', 'motor', 'booking', 'item');
}

// 1. Motor belum kembali: Admin tidak dapat membuat denda manual
test('1. motor belum kembali: tombol request denda tidak ditampilkan di halaman detail', function () {
    extract(createReturnTestContext(isReturned: false));

    $response = $this->actingAs($admin)->get("/admin/bookings/{$booking->id}");
    $response->assertOk()
        ->assertDontSee('+ Request Denda')
        ->assertSee('Sedang Disewa');
});

// 2. Motor sudah kembali: booking berubah menjadi Menunggu Konfirmasi Pengembalian
test('2. motor sudah kembali: status booking di UI menjadi Menunggu Konfirmasi Pengembalian', function () {
    extract(createReturnTestContext(isReturned: true));

    expect($booking->isAllItemsReturned())->toBeTrue();
    expect($booking->uiStatusLabel())->toBe('Menunggu Konfirmasi Pengembalian');
    expect($booking->uiStatusBadgeClass())->toBe('badge-pending');

    $response = $this->actingAs($admin)->get("/admin/bookings/{$booking->id}");
    $response->assertOk()
        ->assertSee('Menunggu Konfirmasi Pengembalian');
});

// 3. Motor sudah kembali: tombol Request Denda tersedia
test('3. motor sudah kembali: tombol Request Denda tersedia di UI admin', function () {
    extract(createReturnTestContext(isReturned: true));

    $response = $this->actingAs($admin)->get("/admin/bookings/{$booking->id}");
    $response->assertOk()
        ->assertSee('+ Request Denda');
});

// 4. Motor belum kembali: endpoint pembuatan denda manual ditolak
test('4. motor belum kembali: endpoint pembuatan denda manual ditolak backend', function () {
    extract(createReturnTestContext(isReturned: false));

    $response = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $item->id,
        'type' => 'damage',
        'description' => 'Lecet spakbor depan',
        'amount' => 50000,
    ]);

    $response->assertSessionHasErrors('booking_item_id');
    expect(Fine::count())->toBe(0);
});

// 5. Booking sudah selesai: pembuatan denda baru ditolak
test('5. booking sudah selesai: pembuatan denda baru ditolak', function () {
    extract(createReturnTestContext(isReturned: true));
    $booking->update(['status' => BookingStatus::Completed]);

    $response = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $item->id,
        'type' => 'damage',
        'description' => 'Kerusakan tambahan',
        'amount' => 50000,
    ]);

    $response->assertSessionHasErrors('booking_item_id');
    expect(Fine::count())->toBe(0);
});

// 6. Tidak ada denda: Admin dapat menyelesaikan booking
test('6. tidak ada denda: Admin dapat menyelesaikan booking', function () {
    extract(createReturnTestContext(isReturned: true));

    expect($booking->isEligibleForCompletion())->toBeTrue();

    $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/complete");
    $response->assertRedirect()
        ->assertSessionHas('success');

    expect($booking->fresh()->status)->toBe(BookingStatus::Completed);
});

// 7. Ada denda unpaid: penyelesaian booking ditolak
test('7. ada denda unpaid: penyelesaian booking ditolak', function () {
    extract(createReturnTestContext(isReturned: true));

    Fine::create([
        'booking_item_id' => $item->id,
        'type' => FineType::Damage,
        'description' => 'Baret knalpot',
        'amount' => 50000,
        'status' => FineStatus::Unpaid,
    ]);

    expect($booking->fresh()->isEligibleForCompletion())->toBeFalse();

    $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/complete");
    $response->assertRedirect()
        ->assertSessionHas('error');

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
});

// 8. Pembayaran menunggu verifikasi: penyelesaian booking ditolak
test('8. pembayaran menunggu verifikasi: penyelesaian booking ditolak', function () {
    extract(createReturnTestContext(isReturned: true));

    $fine = Fine::create([
        'booking_item_id' => $item->id,
        'type' => FineType::Damage,
        'description' => 'Baret knalpot',
        'amount' => 50000,
        'status' => FineStatus::Unpaid,
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'fine_id' => $fine->id,
        'user_id' => $customer->id,
        'payment_type' => 'fine',
        'amount' => 50000,
        'payment_method' => 'BCA',
        'payment_proof' => 'payment-proofs/test.jpg',
        'status' => PaymentStatus::Pending,
    ]);

    expect($booking->fresh()->hasPendingFinePayments())->toBeTrue();
    expect($booking->fresh()->isEligibleForCompletion())->toBeFalse();

    $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/complete");
    $response->assertRedirect()
        ->assertSessionHas('error');

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
});

// 9. Semua denda paid: Admin dapat menyelesaikan booking
test('9. semua denda paid: Admin dapat menyelesaikan booking', function () {
    extract(createReturnTestContext(isReturned: true));

    Fine::create([
        'booking_item_id' => $item->id,
        'type' => FineType::Damage,
        'description' => 'Baret knalpot',
        'amount' => 50000,
        'status' => FineStatus::Paid,
        'paid_at' => now(),
    ]);

    expect($booking->fresh()->isEligibleForCompletion())->toBeTrue();

    $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/complete");
    $response->assertRedirect()
        ->assertSessionHas('success');

    expect($booking->fresh()->status)->toBe(BookingStatus::Completed);
});

// 10. Pembayaran denda tidak otomatis mengubah booking menjadi Completed
test('10. pembayaran denda tidak otomatis mengubah booking menjadi Completed', function () {
    extract(createReturnTestContext(isReturned: true));

    $fine = Fine::create([
        'booking_item_id' => $item->id,
        'type' => FineType::Damage,
        'description' => 'Baret knalpot',
        'amount' => 50000,
        'status' => FineStatus::Unpaid,
    ]);

    $paymentService = app(FinePaymentService::class);
    $payResult = $paymentService->payFine($fine, $customer, 'bank_transfer');

    expect($payResult['booking_completed'])->toBeFalse();
    expect($fine->fresh()->isPaid())->toBeTrue();
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
    expect($booking->fresh()->uiStatusLabel())->toBe('Menunggu Konfirmasi Pengembalian');
});

// 11. Setelah booking selesai, tombol Request Denda tidak tersedia
test('11. setelah booking selesai, tombol Request Denda dan Selesaikan tidak tersedia di UI', function () {
    extract(createReturnTestContext(isReturned: true));
    $booking->update(['status' => BookingStatus::Completed]);

    $response = $this->actingAs($admin)->get("/admin/bookings/{$booking->id}");
    $response->assertOk()
        ->assertDontSee('+ Request Denda')
        ->assertDontSee('Konfirmasi Pemesanan Selesai')
        ->assertSee('Pemesanan Selesai');
});

// 12. Setelah booking selesai, endpoint pembuatan denda menolak request
test('12. setelah booking selesai, endpoint pembuatan denda menolak request', function () {
    extract(createReturnTestContext(isReturned: true));
    $booking->update(['status' => BookingStatus::Completed]);

    $response = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $item->id,
        'type' => 'other',
        'description' => 'Denda susulan',
        'amount' => 20000,
    ]);

    $response->assertSessionHasErrors('booking_item_id');
});

// 13. Admin tidak dapat menyelesaikan booking jika motor belum dikembalikan
test('13. Admin tidak dapat menyelesaikan booking jika motor belum dikembalikan', function () {
    extract(createReturnTestContext(isReturned: false));

    $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/complete");
    $response->assertRedirect()
        ->assertSessionHas('error');

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
});

// 14. Dua request penyelesaian bersamaan tidak menyebabkan status atau histori menjadi tidak konsisten
test('14. dua request penyelesaian berturut-turut ditangani secara aman (idempotent/error)', function () {
    extract(createReturnTestContext(isReturned: true));

    $completionService = app(BookingCompletionService::class);

    // First completion succeeds
    $res1 = $completionService->completeBookingByAdmin($booking, $admin);
    expect($res1['success'])->toBeTrue();
    expect($booking->fresh()->status)->toBe(BookingStatus::Completed);

    // Second completion throws exception because booking is already completed
    expect(fn () => $completionService->completeBookingByAdmin($booking, $admin))
        ->toThrow(InvalidArgumentException::class, 'Pemesanan ini sudah diselesaikan sebelumnya.');
});

// 15. Customer tidak dapat menjalankan aksi khusus Admin
test('15. Customer tidak dapat menjalankan aksi khusus Admin', function () {
    extract(createReturnTestContext(isReturned: true));

    // Customer tries to complete booking
    $this->actingAs($customer)
        ->post("/admin/bookings/{$booking->id}/complete")
        ->assertForbidden();

    // Customer tries to create fine
    $this->actingAs($customer)
        ->post('/admin/fines', [
            'booking_item_id' => $item->id,
            'type' => 'damage',
            'description' => 'Baret knalpot',
            'amount' => 50000,
        ])
        ->assertForbidden();
});
