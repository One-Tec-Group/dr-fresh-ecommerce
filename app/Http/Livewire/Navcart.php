<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Navcart extends Component
{
    public $count = 0;
    protected $listeners = [
        'added_product_to_cart' => 'render',
        'deleted_product_from_cart'=> 'render',
    ];

    public function render()
    {
        if (Auth::guard('customer')->check()) {
            \Cart::session(Auth::guard('customer')->id());
        }

        $this->count = \Cart::getContent()->count();
        return view('livewire.navcart');
    }


}
