<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('fine_id')->nullable()->after('booking_id')->constrained('fines')->cascadeOnDelete();
            $table->string('payment_type', 20)->default('rental')->after('fine_id');
            $table->text('rejection_reason')->nullable()->after('status');
            $table->index(['fine_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['fine_id', 'status']);
            $table->dropForeign(['fine_id']);
            $table->dropColumn(['fine_id', 'payment_type', 'rejection_reason']);
        });
    }
};
