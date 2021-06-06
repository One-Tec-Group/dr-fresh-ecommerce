<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Cartcheckout extends Component
{

    public $cart_items = [];
    public $count = 0;
    public $subtotal = 0;
    public $delivery_cost = 0;
    public $total_with_delivery = 0;

    protected $listeners = [
        'added_product_to_cart' => 'render',
        'delivery_cost',
        'deleted_product_from_cart' => 'render'
    ];


    public function render()
    {
        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());

        }

        $this->cart_items = \Cart::getContent()->toArray();
        $this->count = \Cart::getContent()->count();
        $this->subtotal = \Cart::getSubTotal();
        $this->total_with_delivery = \Cart::getSubTotal() + $this->delivery_cost;

        return view('livewire.cartcheckout');
    }


    public function delivery_cost($cost)
    {
        $this->delivery_cost = $cost;
        $this->render();
    }
}
