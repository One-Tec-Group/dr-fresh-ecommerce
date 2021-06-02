<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryGroups extends Model
{
    protected $table = 'delivery_groups';
    protected $fillable = ['name','price','business_id'];

}
