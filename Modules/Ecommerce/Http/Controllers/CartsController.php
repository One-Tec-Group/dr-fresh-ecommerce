<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Discount;
use App\Product;
use App\DeliveryGroups;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        $price = $this->set_discount($product,$price) ?? $price;


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

    public function set_discount($product ,$price)
    {
        $discount = Discount::with('variations')->where('business_id', config('constants.business_id'))
            ->where('is_active', 1)->where('starts_at', '<=', Carbon::now())
            ->where('ends_at', '>=', Carbon::now())
            ->where(function($query) use ($product){
                $query->where('category_id', $product->category_id)
                ->orWhere('category_id', null);
            })
            ->orderBy('priority', 'desc')
            ->first();

        $price_after_discount =null;
        if (!$discount->category_id){
            foreach ($discount->variations as $variation){
                if ($variation->product_id == $product->id){
                    if($discount->discount_type == 'percentage'){
                        //percentage
                        $dis = $price * $discount->discount_amount/100;
                        $price_after_discount = $price - $dis;

                    }else{
                        //fixed
                        $price_after_discount = $price - $discount->discount_amount;
                    }
                    $price_after_discount = $price_after_discount < 0 ? 0 : $price_after_discount;
                    break;
                }
            }
        }else{

            if($discount->discount_type == 'percentage'){
                //percentage
                $dis = $price * $discount->discount_amount/100;
                $price_after_discount = $price - $dis;

            }else{
                //fixed
                $price_after_discount = $price - $discount->discount_amount;
            }
            $price_after_discount = $price_after_discount < 0 ? 0 : $price_after_discount;
        }

        return $price_after_discount;
    }

    public function decrease($id, $quantity)
    {

        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }


        $product = Product::where('business_id', config('constants.business_id'))->where('id', $id)->with('variation_location_details')->first();
        $check_cart = \Cart::get($id);

        $price = (double)$product->variations->first()->default_sell_price;

        $price = $this->set_discount($product,$price) ?? $price;


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
