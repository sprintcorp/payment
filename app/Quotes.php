<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotes extends Model
{
    //
    protected $table ='payment_details_';
    protected $fillable = ['transaction_reference','sender_account_uri','recipient_account_uri','quote_type.reverse.sender_currency'
        ,'payment_amount.amount','payment_amount.currency','payment_origination_country','payment_type'];
}
