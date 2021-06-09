<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Navcart extends Component
{
    public $count = 0;
    protected $listeners = [
        'added_product_to_cart',
        'deleted_product_from_cart',
        'decrease_product' => 'render',
    ];

    public function render()
    {
        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }

        $this->count = \Cart::getContent()->count();
        return view('livewire.navcart');
    }


    public function added_product_to_cart()
    {
        $this->render();
    }


    public function deleted_product_from_cart()
    {
        $this->render();
    }
}
