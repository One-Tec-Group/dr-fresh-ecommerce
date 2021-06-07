
                <div class="modal-body">
                    <div class="bottom-cart-items">

                        @forelse($cart_items as $item)
                            @php
                                // $cart_item = \App\Product::find($item['id']);
                                $cart_item = $item['attributes']['offer_id'] === null ? \App\Product::find($item['id']) : \Modules\Ecommerce\Entities\Offer::find($item['attributes']['offer_id']);
                            @endphp
                            {{-- {{$cart_item}} --}}
                            <div class="single-cart-item">
                                <div class="item-image">
                                    @if($cart_item->image != null )

                                        <img class="w-100" src="{{env('POS_URL') . "uploads/img/".$cart_item->image}}"
                                             alt="{{$cart_item->name ?? ''}}">
                                    @else
                                        <img class="w-100" src="{{asset('frontend/images/placeholder.png')}}"
                                             alt="{{$cart_item->name ?? ''}}">
                                    @endif
                                </div>
                                <div style="flex:1">
                                    <h5 class="item-name">{{$cart_item->name ?? ''}}</h5>
                                    <div class="d-flex">
                                        <input class="quantity-input" type="number" readonly
                                               value="{{$item['quantity']}}"

                                        >
                                        <button class="quantity-btn" wire:click="decrease({{$cart_item->id}})">
                                            <i class="mdi mdi-minus"></i>
                                        </button>
                                        <button class="quantity-btn" wire:click="increase({{$cart_item->id}})">
                                            <i class="mdi mdi-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-right">
                                        <h5 class="item-price">{{\Cart::get($cart_item->id)->getPriceSum() ?? ''}} @lang('ecommerce::locale.pound')</h5>
                                        {{-- <h5 class="item-price">{{\Cart::get($cart_item->id)->getPriceSum() ?? ''}} @lang('ecommerce::locale.pound')</h5> --}}
                                        <button class="btn" style="background: #e74c3c;
                            color: #fff !important;
                            border: none;
                            min-width: 60px;
                            text-align: center;
                            padding: 2px 5px;
                            border-radius: 2px;" onclick="removeFromCart({{$cart_item->id}})">إزالة
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
