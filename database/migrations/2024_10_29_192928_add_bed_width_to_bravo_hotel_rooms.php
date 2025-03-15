<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('bravo_hotel_rooms', function (Blueprint $table) {
            $table->integer('bed_width_from')->nullable();
            $table->integer('bed_width_to')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bravo_hotel_rooms', function (Blueprint $table) {
            $table->dropColumn(['bed_width_from', 'bed_width_to']);
        });
    }
};
