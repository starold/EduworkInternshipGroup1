<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_category_id')->constrained()->cascadeOnDelete();
            $table->string('merk', 100);
            $table->string('warna', 100);
            $table->string('model', 100);
            $table->year('year');
            $table->string('plate_number', 20)->unique();
            $table->decimal('price_per_day', 12, 2);
            $table->decimal('deposit', 12, 2);
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->enum('status', ['available', 'rented', 'maintenance', 'inactive'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motors');
    }
};
