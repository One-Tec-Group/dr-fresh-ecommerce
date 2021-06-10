<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = "coupons";
    protected $fillable = ['coupon_num','start_date','price','end_date','requiring_user','requiring_all','is_active','price','business_id','created_at','updated_at'];
}
