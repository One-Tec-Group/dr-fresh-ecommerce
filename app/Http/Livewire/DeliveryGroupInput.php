<?php

namespace App\Http\Livewire;

use App\DeliveryGroups;
use Livewire\Component;

class DeliveryGroupInput extends Component
{
    public $addresses;
    public function render()
    {
        $this->addresses =  DeliveryGroups::where('business_id', config('constants.business_id'))->get();
        return view('livewire.delivery-group-input');
    }
}
