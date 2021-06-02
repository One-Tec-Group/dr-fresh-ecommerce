<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryBoyTransaction extends Model
{
        protected $table = 'delivery_boy_transactions';
        protected $fillable = ['transaction_id','delivery_boy_id','delivery_price','address_delivery_id'];

}
