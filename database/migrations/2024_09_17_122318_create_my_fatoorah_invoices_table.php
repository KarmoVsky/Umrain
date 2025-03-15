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
        Schema::create('my_fatoorah_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bravo_booking_payment_id');
            $table->foreign('bravo_booking_payment_id')->references('id')->on('bravo_booking_payments');
            $table->unsignedBigInteger('invoice_id')->unique();
            $table->string('invoice_status');
            $table->string('invoice_reference');
            $table->string('customer_reference');
            $table->timestamp('created_date');
            $table->string('expiry_date'); //error when set is as date, thus replcaed to string
            $table->time('expiry_time');
            $table->decimal('invoice_value', 15, 3);
            $table->text('comments')->nullable();
            $table->string('customer_name');
            $table->string('customer_mobile');
            $table->string('customer_email');
            $table->text('user_defined_field')->nullable();
            $table->string('invoice_display_value');
            $table->decimal('due_deposit', 15, 3);
            $table->string('deposit_status');

            // Fields related to transactions
            $table->timestamp('transaction_date')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('track_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('authorization_id')->nullable();
            $table->string('transaction_status')->nullable();
            $table->decimal('transation_value', 15, 3)->nullable();
            $table->decimal('customer_service_charge', 15, 3)->nullable();
            $table->decimal('total_service_charge', 15, 3)->nullable();
            $table->decimal('due_value', 15, 3)->nullable();
            $table->string('paid_currency')->nullable();
            $table->decimal('paid_currency_value', 15, 3)->nullable();
            $table->decimal('vat_amount', 15, 3)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('country')->nullable();
            $table->string('currency')->nullable();
            $table->string('error')->nullable();
            $table->string('card_number')->nullable();
            $table->string('error_code')->nullable();
            $table->string('invoice_error')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_fatoorah_invoices');
    }
};
