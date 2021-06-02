<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';
    protected $fillable = ['city','delivery_id','near_to','street_no','building_number','floor','apartment_number','special_marque','business_id','contact_id'];

    public function contact()
    {
        return $this->belongsTo(Contact::class,'contact_id');
    }

    public function delivery_group()
    {
        return $this->belongsTo(DeliveryGroups::class,'delivery_id');
    }
    
    
}
