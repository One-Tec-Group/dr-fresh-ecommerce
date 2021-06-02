<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryBoy extends Model
{

    protected $guarded = [];

    public function vehicle(){
        return $this->belongsTo(DeliveryVehicle::class);
    }

}
