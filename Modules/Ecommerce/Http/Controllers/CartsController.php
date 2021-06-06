<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Product;
use App\DeliveryGroups;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CartsController extends Controller
{


    public function add($id, $quantity)
    {


        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }


        $product = Product::where('business_id', config('constants.business_id'))->where('id', $id)->with('variation_location_details')->first();
        $check_cart = \Cart::get($id);


            $price = (double)$product->variations->first()->default_sell_price;
            $wighted = false;


        if ($check_cart) {
            if ($this->check_quantity($product, $check_cart->quantity + $quantity)) {

                $this->update(
                    $id,
                    $quantity,
                    $price
                );
            }

        } else {
            if ($this->check_quantity($product, $quantity)) {

                // add the product to cart
                \Cart::add(array(
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'attributes' => array('weighted' => $wighted),
                    'associatedModel' => $product
                ));
            }

        }



        return response()->json(true, Response::HTTP_CREATED);
    }


    public function remove($id)
    {

        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }

        // remove the product to cart
        \Cart::remove($id);


        return response()->json(true, Response::HTTP_CREATED);
    }

    public function update($id, $qty, $price)
    {
        // update the item on cart
        \Cart::update($id, [
            'quantity' => $qty,
            'price' => $price
        ]);

    }


    public function check_quantity($product, $quantity)
    {
        if ($product->enable_stock == 1) {
            if ($product->variation_location_details->qty_available >= $quantity) {
                return true;
            } else {
                return false;
            }

        } else {
            return true;
        }
    }


    public function checkout()
    {
        $addresses = DeliveryGroups::where('business_id', config('constants.business_id'))->get();
        $current_address=Auth::guard('customer')->user() ? Auth::guard('customer')->user()->contact ? Auth::guard('customer')->user()->contact->last_addresses : '' : '';
        return view('ecommerce::frontend.checkout.checkout', compact('addresses','current_address'));
    }


    public function decrease($id, $quantity)
    {

        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }


        $product = Product::where('business_id', config('constants.business_id'))->where('id', $id)->with('variation_location_details')->first();
        $check_cart = \Cart::get($id);


        $price = (double)$product->variations->first()->default_sell_price;


        if ($check_cart) {


                $this->update(
                    $id,
                    - $quantity,
                    $price
                );

        }



        return response()->json(true, Response::HTTP_CREATED);
    }
    
}
