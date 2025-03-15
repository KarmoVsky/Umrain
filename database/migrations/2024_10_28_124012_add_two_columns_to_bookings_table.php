<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bravo_bookings', function (Blueprint $table) {
            $table->string('payment_gateway')->after('gateway')->nullable();
            $table->string('invoice_status')->after('payment_gateway')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bravo_bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_gateway', 'invoice_status']);
        });
    }
};
