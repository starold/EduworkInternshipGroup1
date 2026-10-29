<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ktp_number', 20)->nullable()->after('address');
            $table->string('sim_number', 20)->nullable()->after('ktp_number');
            $table->string('avatar')->nullable()->after('sim_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ktp_number', 'sim_number', 'avatar']);
        });
    }
};
