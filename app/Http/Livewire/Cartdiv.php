<?php

namespace App\Http\Livewire;

use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Cartdiv extends Component
{

    public $cart_items = [];
    public $count = 0;
    public $subtotal = 0;
    public $total_quantity = 0;
    public $coupon_discount;

    protected $listeners = [
        'added_product_to_cart',
        'deleted_product_from_cart',
        'increase_product',
        'decrease_product'
    ];


    public function render()
    {
        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());

        }

        $this->cart_items = \Cart::getContent()->toArray();
        $this->count = \Cart::getContent()->count();
        $this->subtotal = \Cart::getSubTotal();
        $this->total_quantity = \Cart::getTotalQuantity();

        if (count($this->cart_items) == null) {
            Session::put('coupon_discount', 0);
        } else {
            $this->coupon_discount = Session::get('coupon_discount');
        }


        return view('livewire.cartdiv');
    }



    public function increase_product()
    {
        $this->render();
    }

    public function decrease_product()
    {
        $this->render();
    }

    public function increase($id)
    {

        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }

        $type = null;

        $product = Product::where('business_id', config('constants.business_id'))->where('id', $id)->with('variation_location_details')->first();
        $check_cart = \Cart::get($id);

        if ($check_cart['attributes']['offer_id'] !== null) {
            $type = 'offer';
        } else {
            $type = 'product';
        }

            // $price = (double)$product->variations->first()->default_sell_price;
            $price = (double)$check_cart->price;


        if ($this->check_quantity($product, $check_cart->quantity + 1)) {

            $this->update(
                $id,
                1,
                $price
            );
        }

        $this->emit('increase_product');

        $this->render();
    }

    public function decrease($id)
    {
        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }

        $product = Product::where('business_id', config('constants.business_id'))->where('id', $id)->with('variation_location_details')->first();
        $check_cart = \Cart::get($id);



            // $price = (double)$product->variations->first()->default_sell_price;
            $price = (double)$check_cart->price;


        if ($check_cart->quantity > 1) {

            $this->update(
                $id,
                -1,
                $price
            );
        }
        else{
            $this->remove($id);
        }

       
        $this->emit('decrease_product');

        $this->render();
    }


    public function update($id, $qty, $price)
    {
        // update the item on cart
        \Cart::update($id, [
            'quantity' => $qty,
            'price' => $price
        ]);

    }


    
    public function check_quantity($product, $quantity, $type = null)
    {
        if ($type === 'product') {
            if ($product->enable_stock == 1) {
                if ($product->variation_location_details->qty_available >= $quantity) {
                    return true;
                } else {
                    return false;
                }
    
            } else {
                return true;
            }
        } else {
            return true;
        }
        
    }

    public function remove($id)
    {

        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }

        // remove the product to cart
        \Cart::remove($id);

    }
}
