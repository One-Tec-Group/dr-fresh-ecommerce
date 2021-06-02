<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreProduct extends Model
{
    protected $guarded = [];


    public function business()
    {
        return $this->belongsTo(Business::class,'business_id');
    }


    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class,'store_id');
    }


    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }


}
