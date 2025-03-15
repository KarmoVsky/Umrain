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
        Schema::table('anisth_business', function (Blueprint $table) {
            if (Schema::hasColumn('anisth_business', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (!Schema::hasColumn('users', 'user_id')) {
                $table->unsignedBigInteger('create_user')->nullable()->after('avatar_id');
                $table->foreign('create_user')->references('id')->on('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anisth_business', function (Blueprint $table) {
            $table->dropColumn('create_user');
        });
    }
};
