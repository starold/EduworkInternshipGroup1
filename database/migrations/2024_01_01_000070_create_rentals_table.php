<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_item_id')->constrained()->cascadeOnDelete();
            $table->datetime('pickup_at');
            $table->datetime('due_date');
            $table->enum('status', ['scheduled', 'active', 'returned', 'completed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('rental_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->cascadeOnDelete();
            $table->datetime('returned_at');
            $table->enum('fuel_level', ['empty', 'quarter', 'half', 'three_quarter', 'full']);
            $table->integer('late_minutes')->default(0);
            $table->enum('condition_status', ['good', 'minor_damage', 'damaged'])->default('good');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_returns');
        Schema::dropIfExists('rentals');
    }
};
