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
            Schema::create('anisth_business', function (Blueprint $table) {
                $table->id();
                $table->string('business_name');
                $table->string('business_name_id')->nullable();
                $table->string('country_code', 50)->nullable();
                $table->string('phone')->nullable();

                $table->unique(['country_code', 'phone']);

                $table->string('email');
                $table->timestamp('email_verified_at')->nullable();
                $table->string('address')->nullable();
                $table->string('address2')->nullable();
                $table->string('status');
                $table->string('country');
                $table->string('state');
                $table->string('city');
                $table->string('zip_code')->nullable();
                $table->bigInteger('avatar_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_time')->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('anisth_business');
    }
};
