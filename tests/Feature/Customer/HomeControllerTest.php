<?php

use App\Enums\MotorStatus;
use App\Models\Motor;
use App\Models\MotorCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps motor searches and filters on the vehicle page', function () {
    $category = MotorCategory::create([
        'name' => 'Matic',
        'slug' => 'matic',
    ]);

    Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Yamaha',
        'model' => 'NMAX',
        'warna' => 'Hitam',
        'year' => 2024,
        'plate_number' => 'AB 1234 CD',
        'price_per_day' => 100000,
        'deposit' => 300000,
        'status' => MotorStatus::Available,
    ]);

    Motor::create([
        'motor_category_id' => $category->id,
        'merk' => 'Honda',
        'model' => 'Beat',
        'warna' => 'Putih',
        'year' => 2024,
        'plate_number' => 'AB 2345 CD',
        'price_per_day' => 75000,
        'deposit' => 250000,
        'status' => MotorStatus::Available,
    ]);

    $this->get(route('vehicles.index', ['search' => 'NMAX']))
        ->assertOk()
        ->assertSee('NMAX')
        ->assertDontSee('Beat')
        ->assertSee('action="'.route('vehicles.index').'"', false)
        ->assertSee('name="category"', false);
});

it('filters available motors by vehicle category on the vehicle page', function () {
    $matic = MotorCategory::create([
        'name' => 'Matic',
        'slug' => 'matic',
    ]);
    $manual = MotorCategory::create([
        'name' => 'Manual',
        'slug' => 'manual',
    ]);

    Motor::create([
        'motor_category_id' => $matic->id,
        'merk' => 'Yamaha',
        'model' => 'NMAX',
        'warna' => 'Hitam',
        'year' => 2024,
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
        'year' => 2024,
        'plate_number' => 'AB 4567 CD',
        'price_per_day' => 125000,
        'deposit' => 350000,
        'status' => MotorStatus::Available,
    ]);

    $this->get(route('vehicles.index', ['category' => 'matic']))
        ->assertOk()
        ->assertSee('NMAX')
        ->assertDontSee('CBR')
        ->assertSee('action="'.route('vehicles.index').'"', false);
});
