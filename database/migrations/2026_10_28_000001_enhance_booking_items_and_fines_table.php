<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dateTime('returned_at')->nullable()->after('deposit');
            $table->enum('return_status', ['not_returned', 'returned'])->default('not_returned')->after('returned_at');
            $table->index('return_status');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('started_at')->nullable()->after('paid_at');
            $table->timestamp('ended_at')->nullable()->after('started_at');
            $table->integer('days_late')->default(0)->after('ended_at');
            $table->string('payment_method', 50)->nullable()->after('days_late');
            $table->string('transaction_reference', 100)->nullable()->after('payment_method');

            $table->index(['booking_item_id', 'type']);
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->dropIndex(['booking_item_id', 'type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['type']);
            $table->dropColumn([
                'paid_at',
                'started_at',
                'ended_at',
                'days_late',
                'payment_method',
                'transaction_reference',
            ]);
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropIndex(['return_status']);
            $table->dropColumn(['returned_at', 'return_status']);
        });
    }
};
