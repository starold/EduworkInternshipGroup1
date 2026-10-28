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
use App\Services\ReturnConfirmationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function setupFineTestEntities(): array
{
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $otherCustomer = User::factory()->create(['role' => UserRole::Customer]);

    $category = MotorCategory::firstOrCreate(
        ['slug' => 'matic'],
        ['name' => 'Matic', 'description' => 'Motor Matic']
    );

    $motor = Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Honda',
        'model' => 'PCX 160',
        'warna' => 'Putih',
        'year' => 2024,
        'plate_number' => 'AB ' . rand(1000, 9999) . ' ZZ',
        'price_per_day' => 120000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);

    $booking = Booking::create([
        'user_id' => $customer->id,
        'booking_code' => 'BK-' . strtoupper(uniqid()),
        'start_date' => Carbon::parse('2026-10-10'),
        'end_date' => Carbon::parse('2026-10-12'),
        'pickup_method' => 'pickup',
        'subtotal' => 240000,
        'delivery_fee' => 0,
        'discount' => 0,
        'total_price' => 240000,
        'status' => BookingStatus::Confirmed,
    ]);

    $bookingItem = $booking->items()->create([
        'motor_id' => $motor->id,
        'price_per_day' => 120000,
        'due_date' => Carbon::parse('2026-10-12 10:00:00'),
        'total_days' => 2,
        'subtotal' => 240000,
        'deposit' => 300000,
        'return_status' => ReturnStatus::Returned,
        'returned_at' => Carbon::parse('2026-10-12 09:30:00'),
    ]);

    return compact('admin', 'customer', 'otherCustomer', 'motor', 'booking', 'bookingItem');
}

// 1. Nominal Rp9.999 ditolak
test('1. nominal Rp9.999 ditolak (di bawah batas minimum Rp10.000)', function () {
    extract(setupFineTestEntities());

    $response = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $bookingItem->id,
        'type' => 'damage',
        'description' => 'Goresan spion',
        'amount' => 9999,
    ]);

    $response->assertSessionHasErrors('amount');
    expect(Fine::count())->toBe(0);
});

// 2. Nominal Rp10.000 diterima
test('2. nominal Rp10.000 diterima (batas minimum)', function () {
    extract(setupFineTestEntities());

    $response = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $bookingItem->id,
        'type' => 'damage',
        'description' => 'Goresan spion',
        'amount' => 10000,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('fines', [
        'booking_item_id' => $bookingItem->id,
        'amount' => '10000.00',
    ]);
});

// 3. Nominal Rp29.001 dan Rp30.001 diproses tanpa perubahan nilai
test('3. nominal Rp29.001 dan Rp30.001 diproses tanpa perubahan nilai yang tidak diinginkan', function () {
    extract(setupFineTestEntities());

    // Test raw integer 29001
    $resp1 = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $bookingItem->id,
        'type' => 'damage',
        'description' => 'Kerusakan kabel spidometer',
        'amount' => 29001,
    ]);
    $resp1->assertRedirect();

    // Test raw integer 30001
    $resp2 = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $bookingItem->id,
        'type' => 'other',
        'description' => 'Biaya cuci khusus',
        'amount' => 30001,
    ]);
    $resp2->assertRedirect();

    // Test Indonesian formatted string "29.001"
    $resp3 = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $bookingItem->id,
        'type' => 'other',
        'description' => 'Biaya tambahan helm',
        'amount' => '29.001',
    ]);
    $resp3->assertRedirect();

    // Test Indonesian formatted string "30.001"
    $resp4 = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $bookingItem->id,
        'type' => 'damage',
        'description' => 'Ganti lampu sen',
        'amount' => '30.001',
    ]);
    $resp4->assertRedirect();

    // Verify exact storage in database
    $fines = Fine::all();
    expect((float) $fines[0]->amount)->toBe(29001.0);
    expect((float) $fines[1]->amount)->toBe(30001.0);
    expect((float) $fines[2]->amount)->toBe(29001.0);
    expect((float) $fines[3]->amount)->toBe(30001.0);
});

// 4. Nominal Rp100.000.000 diterima
test('4. nominal Rp100.000.000 diterima (batas maksimum)', function () {
    extract(setupFineTestEntities());

    $response = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $bookingItem->id,
        'type' => 'damage',
        'description' => 'Kerusakan mesin total',
        'amount' => 100000000,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('fines', [
        'booking_item_id' => $bookingItem->id,
        'amount' => '100000000.00',
    ]);
});

// 5. Nominal Rp100.000.001 ditolak
test('5. nominal Rp100.000.001 ditolak (melebihi batas maksimum)', function () {
    extract(setupFineTestEntities());

    $response = $this->actingAs($admin)->post('/admin/fines', [
        'booking_item_id' => $bookingItem->id,
        'type' => 'damage',
        'description' => 'Kerusakan',
        'amount' => 100000001,
    ]);

    $response->assertSessionHasErrors('amount');
    expect(Fine::count())->toBe(0);
});

// 6. User dapat memilih metode pembayaran dan mengunggah bukti
test('6. user dapat memilih metode pembayaran dan mengunggah bukti', function () {
    Storage::fake('public');
    extract(setupFineTestEntities());

    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Lecet fairing',
        'amount' => 50000,
        'status' => FineStatus::Unpaid,
    ]);

    $file = UploadedFile::fake()->create('bukti_transfer.jpg', 150, 'image/jpeg');

    $response = $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'BCA',
        'payment_proof' => $file,
    ]);

    $response->assertRedirect();

    $payment = Payment::where('fine_id', $fine->id)->first();
    expect($payment)->not->toBeNull();
    expect($payment->payment_method)->toBe('BCA');
    expect($payment->status)->toBe(PaymentStatus::Pending);
    expect($payment->payment_proof)->not->toBeNull();
    Storage::disk('public')->assertExists($payment->payment_proof);
});

// 7. User tidak dapat mengonfirmasi pembayaran tanpa bukti
test('7. user tidak dapat mengonfirmasi pembayaran tanpa bukti', function () {
    extract(setupFineTestEntities());

    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Lecet fairing',
        'amount' => 50000,
        'status' => FineStatus::Unpaid,
    ]);

    $response = $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'BCA',
    ]);

    $response->assertSessionHasErrors('payment_proof');
    expect(Payment::where('fine_id', $fine->id)->count())->toBe(0);
});

// 8. Pembayaran yang diajukan tidak langsung mengubah denda menjadi paid
test('8. pembayaran yang diajukan tidak langsung mengubah denda menjadi paid', function () {
    Storage::fake('public');
    extract(setupFineTestEntities());

    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Spion retak',
        'amount' => 60000,
        'status' => FineStatus::Unpaid,
    ]);

    $file = UploadedFile::fake()->create('bukti.png', 100, 'image/png');

    $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'Mandiri',
        'payment_proof' => $file,
    ]);

    $fine->refresh();
    // Fine status MUST remain unpaid
    expect($fine->status)->toBe(FineStatus::Unpaid);
    expect($fine->isAwaitingVerification())->toBeTrue();
    expect($fine->payment->status)->toBe(PaymentStatus::Pending);
});

// 9. Admin dapat melihat bukti pembayaran
test('9. admin dapat melihat bukti pembayaran pada halaman booking dan fines', function () {
    Storage::fake('public');
    extract(setupFineTestEntities());

    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Kunci kontak patah',
        'amount' => 75000,
        'status' => FineStatus::Unpaid,
    ]);

    $file = UploadedFile::fake()->create('struk.jpg', 100, 'image/jpeg');
    $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'BRI',
        'payment_proof' => $file,
    ]);

    $respBooking = $this->actingAs($admin)->get("/admin/bookings/{$booking->id}");
    $respBooking->assertOk();
    $respBooking->assertSee('Menunggu Verifikasi');
    $respBooking->assertSee('BRI');

    $respFines = $this->actingAs($admin)->get('/admin/fines');
    $respFines->assertOk();
    $respFines->assertSee('Menunggu Verifikasi');
});

// 10. Admin dapat mengonfirmasi pembayaran lunas
test('10. admin dapat mengonfirmasi pembayaran lunas', function () {
    Storage::fake('public');
    extract(setupFineTestEntities());

    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Footstep patah',
        'amount' => 80000,
        'status' => FineStatus::Unpaid,
    ]);

    $file = UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg');
    $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'BCA',
        'payment_proof' => $file,
    ]);

    // Admin verifies
    $response = $this->actingAs($admin)->post("/admin/fines/{$fine->id}/verify");
    $response->assertRedirect();

    $fine->refresh();
    expect($fine->status)->toBe(FineStatus::Paid);
    expect($fine->paid_at)->not->toBeNull();
    expect($fine->payment->status)->toBe(PaymentStatus::Paid);
    expect($fine->payment->paid_at)->not->toBeNull();
});

// 11. Admin dapat menolak bukti pembayaran jika fitur penolakan diterapkan
test('11. admin dapat menolak bukti pembayaran dengan alasan penolakan', function () {
    Storage::fake('public');
    extract(setupFineTestEntities());

    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Cover knalpot lecet',
        'amount' => 45000,
        'status' => FineStatus::Unpaid,
    ]);

    $file = UploadedFile::fake()->create('buram.jpg', 50, 'image/jpeg');
    $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'GoPay',
        'payment_proof' => $file,
    ]);

    // Admin rejects
    $response = $this->actingAs($admin)->post("/admin/fines/{$fine->id}/reject", [
        'rejection_reason' => 'Nominal transfer tidak sesuai dengan denda',
    ]);
    $response->assertRedirect();

    $fine->refresh();
    expect($fine->status)->toBe(FineStatus::Unpaid);
    expect($fine->payment->status)->toBe(PaymentStatus::Failed);
    expect($fine->payment->rejection_reason)->toBe('Nominal transfer tidak sesuai dengan denda');
});

// 12. User dapat mengajukan ulang pembayaran setelah penolakan
test('12. user dapat mengajukan ulang pembayaran setelah penolakan', function () {
    Storage::fake('public');
    extract(setupFineTestEntities());

    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Plat nomor bengkok',
        'amount' => 40000,
        'status' => FineStatus::Unpaid,
    ]);

    $file1 = UploadedFile::fake()->create('salah.jpg', 50, 'image/jpeg');
    $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'GoPay',
        'payment_proof' => $file1,
    ]);

    // Admin rejects
    $this->actingAs($admin)->post("/admin/fines/{$fine->id}/reject", [
        'rejection_reason' => 'Struk buram',
    ]);

    // Customer re-uploads new proof
    $file2 = UploadedFile::fake()->create('benar.jpg', 120, 'image/jpeg');
    $reUpload = $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'BCA',
        'payment_proof' => $file2,
    ]);
    $reUpload->assertRedirect();

    $fine->refresh();
    expect($fine->payment->status)->toBe(PaymentStatus::Pending);
    expect($fine->payment->payment_method)->toBe('BCA');
    expect($fine->payment->rejection_reason)->toBeNull();
});

// 13. User tidak dapat membayar denda milik orang lain
test('13. user tidak dapat mengakses atau membayar denda milik pengguna lain', function () {
    Storage::fake('public');
    extract(setupFineTestEntities());

    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Denda customer',
        'amount' => 70000,
        'status' => FineStatus::Unpaid,
    ]);

    // Other customer tries to view payment page
    $this->actingAs($otherCustomer)->get("/fines/{$fine->id}/payment")->assertForbidden();

    // Other customer tries to upload proof
    $file = UploadedFile::fake()->create('palsu.jpg', 100, 'image/jpeg');
    $this->actingAs($otherCustomer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'BCA',
        'payment_proof' => $file,
    ])->assertForbidden();

    expect(Payment::where('fine_id', $fine->id)->count())->toBe(0);
});

// 14. Pembayaran yang sama tidak dapat dikonfirmasi dua kali
test('14. pembayaran yang sama tidak dapat dikonfirmasi dua kali', function () {
    Storage::fake('public');
    extract(setupFineTestEntities());

    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Ganti oli akibat kebocoran',
        'amount' => 55000,
        'status' => FineStatus::Unpaid,
    ]);

    $file = UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg');
    $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'BCA',
        'payment_proof' => $file,
    ]);

    // First verify: success
    $this->actingAs($admin)->post("/admin/fines/{$fine->id}/verify")->assertSessionHas('success');

    // Second verify attempt: returns error
    $resp2 = $this->actingAs($admin)->post("/admin/fines/{$fine->id}/verify");
    $resp2->assertSessionHas('error');
});

// 15. Booking baru menjadi Completed setelah semua syarat penyelesaian terpenuhi
test('15. booking baru menjadi Completed setelah pengembalian dan semua denda diverifikasi lunas', function () {
    Storage::fake('public');
    extract(setupFineTestEntities());

    // 1. Motor is returned by admin
    $bookingItem->update(['return_status' => ReturnStatus::NotReturned, 'returned_at' => null]);
    app(ReturnConfirmationService::class)->confirmReturn($bookingItem, now());
    Fine::where('booking_item_id', $bookingItem->id)->where('type', FineType::Late)->delete();

    // 2. Admin creates a damage fine of Rp 150.000
    $fine = Fine::create([
        'booking_item_id' => $bookingItem->id,
        'type' => FineType::Damage,
        'description' => 'Pecah spakbor depan',
        'amount' => 150000,
        'status' => FineStatus::Unpaid,
    ]);

    // Booking must NOT be completed yet
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);

    // 3. Customer uploads payment proof
    $file = UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg');
    $this->actingAs($customer)->post("/fines/{$fine->id}/payment/proof", [
        'payment_method' => 'BCA',
        'payment_proof' => $file,
    ]);

    // Booking still must NOT be completed (pending verification)
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);

    // 4. Admin verifies payment as paid
    $this->actingAs($admin)->post("/admin/fines/{$fine->id}/verify");

    // 5. Booking remains Confirmed (Menunggu Konfirmasi Pengembalian)
    expect($fine->fresh()->isPaid())->toBeTrue();
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
    expect($booking->fresh()->isEligibleForCompletion())->toBeTrue();

    // 6. Admin explicitly completes the booking
    $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/complete");
    expect($booking->fresh()->status)->toBe(BookingStatus::Completed);
});
