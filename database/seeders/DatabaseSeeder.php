<?php

namespace Database\Seeders;

use App\Enums\MotorStatus;
use App\Enums\UserRole;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin ED.RENT',
            'email' => 'admin@edrent.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Jl. Malioboro No. 1, Yogyakarta',
            'role' => UserRole::Admin,
        ]);

        // Customer demo
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'phone' => '089675435567',
            'address' => 'Jl. Kaliurang KM 5, Yogyakarta',
            'role' => UserRole::Customer,
        ]);

        // Motor Categories
        $categories = [
            ['name' => 'Matic', 'slug' => 'matic', 'description' => 'Motor matic nyaman untuk berkendara di kota'],
            ['name' => 'Manual', 'slug' => 'manual', 'description' => 'Motor manual untuk pengalaman berkendara yang lebih sporty'],
            ['name' => 'Sport', 'slug' => 'sport', 'description' => 'Motor sport bertenaga tinggi'],
            ['name' => 'Trail', 'slug' => 'trail', 'description' => 'Motor trail untuk medan off-road'],
        ];

        foreach ($categories as $category) {
            MotorCategory::create($category);
        }

        $maticId = MotorCategory::where('slug', 'matic')->first()->id;
        $manualId = MotorCategory::where('slug', 'manual')->first()->id;
        $sportId = MotorCategory::where('slug', 'sport')->first()->id;

        // Motors
        $motors = [
            [
                'motor_category_id' => $maticId,
                'merk' => 'Honda',
                'warna' => 'Biru Navy',
                'model' => 'Genio',
                'year' => 2023,
                'plate_number' => 'AB 1234 BC',
                'price_per_day' => 75000,
                'deposit' => 300000,
                'description' => 'Honda Genio matic stylish dan irit bahan bakar. Cocok untuk perjalanan harian di kota Yogyakarta.',
                'status' => MotorStatus::Available,
            ],
            [
                'motor_category_id' => $maticId,
                'merk' => 'Honda',
                'warna' => 'Putih',
                'model' => 'Beat',
                'year' => 2022,
                'plate_number' => 'AB 5678 CD',
                'price_per_day' => 70000,
                'deposit' => 300000,
                'description' => 'Honda Beat matic populer, irit dan nyaman untuk kota.',
                'status' => MotorStatus::Available,
            ],
            [
                'motor_category_id' => $maticId,
                'merk' => 'Yamaha',
                'warna' => 'Hitam',
                'model' => 'NMAX',
                'year' => 2023,
                'plate_number' => 'AB 9012 EF',
                'price_per_day' => 150000,
                'deposit' => 500000,
                'description' => 'Yamaha NMAX premium matic dengan fitur canggih dan desain sporty.',
                'status' => MotorStatus::Available,
            ],
            [
                'motor_category_id' => $manualId,
                'merk' => 'Honda',
                'warna' => 'Merah',
                'model' => 'Supra GTR',
                'year' => 2021,
                'plate_number' => 'AB 3456 GH',
                'price_per_day' => 80000,
                'deposit' => 350000,
                'description' => 'Honda Supra GTR manual yang sporty dan bertenaga.',
                'status' => MotorStatus::Available,
            ],
            [
                'motor_category_id' => $sportId,
                'merk' => 'Honda',
                'warna' => 'Hitam Merah',
                'model' => 'CBR150R',
                'year' => 2022,
                'plate_number' => 'AB 7890 IJ',
                'price_per_day' => 200000,
                'deposit' => 700000,
                'description' => 'Honda CBR150R sport dengan performa tinggi dan tampilan racing.',
                'status' => MotorStatus::Available,
            ],
            [
                'motor_category_id' => $maticId,
                'merk' => 'Yamaha',
                'warna' => 'Biru',
                'model' => 'Aerox',
                'year' => 2023,
                'plate_number' => 'AB 2468 KL',
                'price_per_day' => 130000,
                'deposit' => 500000,
                'description' => 'Yamaha Aerox matic sporty dengan desain aggressive.',
                'status' => MotorStatus::Available,
            ],
        ];

        foreach ($motors as $motor) {
            Motor::create($motor);
        }
    }
}
