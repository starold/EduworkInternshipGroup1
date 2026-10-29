<?php

use App\Enums\MotorStatus;
use App\Enums\UserRole;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps motor search results inside the selected category and status filters', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $matic = MotorCategory::create(['name' => 'Matic', 'slug' => 'matic']);
    $manual = MotorCategory::create(['name' => 'Manual', 'slug' => 'manual']);
    Motor::create([
        'motor_category_id' => $matic->id,
        'merk' => 'Yamaha',
        'model' => 'NMAX',
        'warna' => 'Hitam',
        'year' => 2025,
        'plate_number' => 'AB 3456 CD',
        'price_per_day' => 100000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);
    Motor::create([
        'motor_category_id' => $manual->id,
        'merk' => 'Honda',
        'model' => 'CBR',
        'warna' => 'Merah',
        'year' => 2025,
        'plate_number' => 'AB 6543 CD',
        'price_per_day' => 125000,
        'deposit' => 350000,
        'status' => MotorStatus::Available,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.motors.index', [
            'category' => $matic->id,
            'status' => 'available',
            'search' => 'CBR',
        ]))
        ->assertOk()
        ->assertDontSee('Honda CBR')
        ->assertSee('Tipe kendaraan')
        ->assertSee('name="category"', false);
});

it('keeps motor form controls within their boxes and shows the requested admin actions', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $category = MotorCategory::create(['name' => 'Matic', 'slug' => 'matic']);
    $motor = Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Yamaha',
        'model' => 'NMAX',
        'warna' => 'Hitam',
        'year' => 2025,
        'plate_number' => 'AB 7654 CD',
        'price_per_day' => 100000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.motors.index'))
        ->assertOk()
        ->assertSee('Search')
        ->assertSee('Delete')
        ->assertDontSee('>×</button>', false);

    $this->get(route('admin.motors.create'))
        ->assertOk()
        ->assertSee('max-width:100%', false)
        ->assertSee('textarea { resize:vertical; }', false);

    $this->get(route('admin.motors.edit', $motor))
        ->assertOk()
        ->assertSee('max-width:100%', false)
        ->assertSee('textarea { resize:vertical; }', false);
});
