<?php

namespace Modules\Ecommerce\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    //

     public function index()
    {

        return view('ecommerce::frontend.home.home');
    }

}
