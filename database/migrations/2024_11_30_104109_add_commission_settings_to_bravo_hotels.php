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
        Schema::table('bravo_hotels', function (Blueprint $table) {
            $table->integer('hotel_commission_amount')->nullable();
            $table->string('hotel_commission_type',30)->nullable();
            $table->string('hotel_commission_calculate_way', 30)->nullable();
            $table->string('hotel_commission_calculate_time', 30)->nullable();
            $table->string('hotel_per_person', 15);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bravo_hotels', function (Blueprint $table) {
            $table->dropColumn('hotel_commission_amount');
            $table->dropColumn('hotel_commission_type');
            $table->dropColumn('hotel_commission_calculate_way');
            $table->dropColumn('hotel_commission_calculate_time');
            $table->dropColumn('hotel_per_person');
        });
    }
};
