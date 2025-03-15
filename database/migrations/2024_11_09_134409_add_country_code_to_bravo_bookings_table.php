<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bravo_bookings', function (Blueprint $table) {
            $table->string('country_code', 50)->nullable()->after('phone');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bravo_bookings', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
    }
};
