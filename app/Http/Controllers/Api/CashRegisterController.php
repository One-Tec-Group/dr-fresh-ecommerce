<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CashRegisterController extends Controller
{
    //
     
    public function create(){
         //like:repair
        //  $sub_type = request()->get('sub_type');

         //Check if there is a open register, if yes then redirect to POS screen.
         if ($this->cashRegisterUtil->countOpenedRegister() != 0) {
             return redirect()->action('SellPosController@create', ['sub_type' => $sub_type]);
         }
         $business_id = request()->session()->get('user.business_id');
         $business_locations = BusinessLocation::forDropdown($business_id);
    }
}
