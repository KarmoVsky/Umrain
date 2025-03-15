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
        Schema::create('anisth_business_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('anisth_business');
            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('id')->on('core_roles');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('service_id');
            $table->string('service_type');
            $table->string('sub_region')->nullable();
            $table->string('country')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'draft', 'delete'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anisth_room_prices');
    }
};
