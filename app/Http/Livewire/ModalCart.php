<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Product;
use Illuminate\Support\Facades\Auth;
use Modules\Ecommerce\Http\Controllers\CartsController;

class ModalCart extends Component
{

    public $cart_items = [];
    public $count = 0;
    public $subtotal = 0;
    public $total_quantity = 0;

    protected $listeners = [
        'added_product_to_cart',
        'deleted_product_from_cart'
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

        return view('livewire.modal-cart');
    }


    public function added_product_to_cart()
    {
        $this->render();
    }

    public function deleted_product_from_cart()
    {
        $this->render();
    }

    public function increase($id)
    {

        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }

        $product = Product::where('business_id', config('constants.business_id'))->where('id', $id)->with('variation_location_details')->first();
        $check_cart = \Cart::get($id);




            $price = (double)$product->variations->first()->default_sell_price;
        $cart_controller = new CartsController();
        $price = $cart_controller->set_discount($product,$price) ?? $price;


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



            $price = (double)$product->variations->first()->default_sell_price;
        $cart_controller = new CartsController();
        $price = $cart_controller->set_discount($product,$price) ?? $price;

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


    public function remove($id)
    {

        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }

        // remove the product to cart
        \Cart::remove($id);

    }
}
