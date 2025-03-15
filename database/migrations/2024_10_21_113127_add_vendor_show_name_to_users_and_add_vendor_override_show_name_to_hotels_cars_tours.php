<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up()
        {
            Schema::table('bravo_hotels', function (Blueprint $table) {
                $table->string('vendor_override_show_name')->default('by_default')->after('author_id');
            });

            Schema::table('bravo_cars', function (Blueprint $table) {
                $table->string('vendor_override_show_name')->default('by_default')->after('author_id');
            });

            Schema::table('bravo_tours', function (Blueprint $table) {
                $table->string('vendor_override_show_name')->default('by_default')->after('author_id');
            });
        }

        public function down()
        {
            Schema::table('bravo_hotels', function (Blueprint $table) {
                $table->dropColumn('vendor_override_show_name');
            });

            Schema::table('bravo_cars', function (Blueprint $table) {
                $table->dropColumn('vendor_override_show_name');
            });

            Schema::table('bravo_tours', function (Blueprint $table) {
                $table->dropColumn('vendor_override_show_name');
            });
        }

};
