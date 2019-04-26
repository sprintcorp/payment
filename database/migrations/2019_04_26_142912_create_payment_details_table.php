<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_reference');
            $table->string('sender_account_uri');
            $table->string('recipient_account_uri');
            $table->string('quote_type.reverse.sender_currency');
            $table->string('payment_amount.amount');
            $table->string('payment_amount.currency');
            $table->string('payment_origination_country');
            $table->string('payment_type');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
}
