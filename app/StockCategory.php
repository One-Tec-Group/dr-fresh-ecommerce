<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockCategory extends Model
{
    protected $guarded = [];


    public function business()
    {
        return $this->belongsTo(Business::class,'business_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
