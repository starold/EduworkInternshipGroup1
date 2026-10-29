<?php

use App\Enums\MotorStatus;
use App\Enums\UserRole;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the admin dashboard summary and popular vehicles', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $category = MotorCategory::create(['name' => 'Matic', 'slug' => 'matic']);
    Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Yamaha',
        'model' => 'NMAX',
        'warna' => 'Hitam',
        'year' => 2025,
        'plate_number' => 'AB 2345 CD',
        'price_per_day' => 100000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('5 Motor Paling Sering Disewa')
        ->assertSee('Jumlah Kendaraan')
        ->assertSee('Pendapatan')
        ->assertSee('Yamaha NMAX')
        ->assertSee('Daftar Pesanan');
});
