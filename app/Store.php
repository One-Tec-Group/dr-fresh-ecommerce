<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $guarded = [];


    public function business()
    {
        return $this->belongsTo(Business::class,'business_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class,'categories_store','store_id','category_id');
    }

}
