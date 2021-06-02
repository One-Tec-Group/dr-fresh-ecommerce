<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';
    protected $guarded = [];

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function category()
    {
        return $this->belongsTo(StockCategory::class,'category_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class,'business_id');
    }

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class,'location_id');
    }
}
