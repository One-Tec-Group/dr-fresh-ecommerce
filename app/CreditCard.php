<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    //
    protected $table = 'credit_cards';
    protected $guarded = [];
       /**
     * Get the business that owns the user.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }
       /**
     * Get the business that owns the user.
     */
    public function customer()
    {
        return $this->belongsTo(\App\Customer::class);
    }
}
