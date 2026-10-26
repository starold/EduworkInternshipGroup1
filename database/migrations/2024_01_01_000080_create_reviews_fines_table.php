<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_items_id')->unique()->constrained('booking_items')->cascadeOnDelete();
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_item_id')->constrained('booking_items')->cascadeOnDelete();
            $table->enum('type', ['late', 'damage', 'other']);
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
        Schema::dropIfExists('reviews');
    }
};
