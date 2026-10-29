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
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin reviews route returns 404 and review menu is not present', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get('/admin/reviews')
        ->assertNotFound();

    $resp = $this->actingAs($admin)->get('/admin/dashboard');
    $resp->assertOk()
        ->assertDontSee('Manajemen Review')
        ->assertSee('ED.RENT')
        ->assertSee('Butuh bantuan?')
        ->assertSee('https://wa.me/6281234567890');
});

test('admin topbar matches customer topbar structure and items', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    // Admin view
    $adminResp = $this->actingAs($admin)->get('/admin/dashboard');
    $adminResp->assertOk()
        ->assertSee('Home')
        ->assertSee('Daftar Kendaraan')
        ->assertSee('Daftar Pesanan')
        ->assertSee('Butuh bantuan?');

    // Customer view
    $custResp = $this->actingAs($customer)->get('/bookings');
    $custResp->assertOk()
        ->assertSee('Home')
        ->assertSee('Pilih Kendaraan')
        ->assertSee('Daftar Pesanan')
        ->assertSee('Butuh bantuan?');
});

test('detail denda dan pembayaran button and table scrolling are available on admin and customer pages', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $cat = MotorCategory::create(['name' => 'Matic', 'slug' => 'matic']);
    $motor = Motor::create([
        'motor_category_id' => $cat->id,
        'merk' => 'Honda',
        'model' => 'Vario 160',
        'warna' => 'Hitam',
        'year' => 2025,
        'plate_number' => 'AB 9999 CD',
        'price_per_day' => 100000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);

    $booking = Booking::create([
        'user_id' => $customer->id,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
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
        'due_date' => now()->addDays(2),
        'total_days' => 2,
        'subtotal' => 200000,
        'deposit' => 300000,
        'return_status' => ReturnStatus::NotReturned,
    ]);

    $fine = Fine::create([
        'booking_item_id' => $item->id,
        'type' => FineType::Damage,
        'amount' => 50000,
        'description' => 'Lecet spion kanan',
        'status' => FineStatus::Unpaid,
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'fine_id' => $fine->id,
        'user_id' => $customer->id,
        'amount' => 50000,
        'payment_method' => 'BCA',
        'payment_proof' => 'fine_proofs/test_bca.jpg',
        'status' => PaymentStatus::Pending,
    ]);

    // Admin Fines page
    $adminFinesResp = $this->actingAs($admin)->get('/admin/fines');
    $adminFinesResp->assertOk()
        ->assertSee('Detail Denda &amp; Pembayaran', false)
        ->assertSee('table-scroll-wrapper')
        ->assertSee('table-scroll-body');

    // Admin Booking detail page
    $adminBookingResp = $this->actingAs($admin)->get("/admin/bookings/{$booking->id}");
    $adminBookingResp->assertOk()
        ->assertSee('Detail Denda &amp; Pembayaran', false)
        ->assertSee('table-scroll-wrapper')
        ->assertSee('table-scroll-body');

    // Customer Fines page
    $customerFinesResp = $this->actingAs($customer)->get(route('customer.fines.index'));
    $customerFinesResp->assertOk()
        ->assertSee('Detail Denda &amp; Pembayaran', false)
        ->assertSee('table-scroll-wrapper')
        ->assertSee('table-scroll-body');
});
