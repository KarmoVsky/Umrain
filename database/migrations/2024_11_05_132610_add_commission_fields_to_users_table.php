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
        Schema::table('users', function (Blueprint $table) {
            $table->string('vendor_commission_calculate_way', 30)->nullable();
            $table->string('vendor_commission_calculate_time', 30)->nullable();
            $table->string('per_person', 15);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('vendor_commission_calculate_way');
            $table->dropColumn('vendor_commission_calculate_time');
            $table->dropColumn('per_person');
        });
    }
};


