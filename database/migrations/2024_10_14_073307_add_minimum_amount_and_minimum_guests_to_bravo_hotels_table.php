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
            $table->decimal('minimum_amount', 12, 2)->nullable()->after('sale_price');
            $table->integer('minimum_guests')->nullable()->after('minimum_amount');
            $table->string('condition_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bravo_hotels', function (Blueprint $table) {
            $table->dropColumn('minimum_amount');
            $table->dropColumn('minimum_guests');
            $table->dropColumn('condition_type');
        });
    }
};
