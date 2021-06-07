<?php

namespace Modules\Ecommerce\Entities;

use App\Product;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    
    public function scopeValidOffers($query, $todayDate)
    {
        return $query->where('from', '<=', $todayDate)->where('to', '>=', $todayDate)->where('status', 'active')->with('product')->get();
    }
}
