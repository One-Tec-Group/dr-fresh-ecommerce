<div>

    <div class="cart-sidebar-header">
        <h5>
            @lang('ecommerce::locale.my_cart') <span class="text-success">({{$count}})</span> <a id="offcanvas"
                                                                                                 data-toggle="offcanvas"
                                                                                                 class="float-right"
                                                                                                 href="#"><i
                        class="mdi mdi-close"></i>
            </a>
        </h5>
    </div>
    <div class="cart-sidebar-body">
        @forelse($cart_items as $item)
            @php
                // $cart_item = \App\Product::find($item['id']);
                $cart_item = $item['attributes']['offer_id'] === null ? \App\Product::find($item['id']) : \Modules\Ecommerce\Entities\Offer::find($item['attributes']['offer_id']);

            @endphp
            <div class="cart-list-product">
                <div class="d-flex justify-content-between">
                    <div class="d-flex">
                        <div>

                        <!-- <a class="float-right remove-cart" onclick="removeFromCart({{$cart_item->id}})" href="#"><i
                                    class="mdi mdi-close md-24"></i></a> -->
                        @if($cart_item->image != null )

                                <img class="img-fluid" src="{{env('POS_URL') . "uploads/img/".$cart_item->image}}"
                                alt="{{$cart_item->name ?? ''}}">
                                
                        @else
                            <img class="img-fluid" src="{{asset('frontend/images/placeholder.png')}}"
                                alt="{{$cart_item->name ?? ''}}">
                        @endif
                        </div>
                        <div>

                            {{--<span class="badge badge-success">50% OFF</span>--}}
                            <h5>{{$cart_item->name ?? ''}}</h5>
                            <h6><strong><span class="mdi mdi-approval"></span> @lang('ecommerce::locale.available_in')
                                </strong> {{ $cart_item->unit ? $cart_item->unit->actual_name : $cart_item->name }}</h6>
                            <p class="offer-price mb-0"> {{(double)$item['attributes']['offer_id'] === null ? $cart_item->variations->first()->default_sell_price : \Cart::get($cart_item->id)->getPriceSum()}} @lang('ecommerce::locale.pound') </p>
                            {{-- <p class="offer-price mb-0"> {{$cart_item>}} @lang('ecommerce::locale.pound') </p> --}}
                            <div class="d-flex">
                                <button class="btn btn-theme-round btn-number" type="button" wire:click="decrease({{$cart_item->id}})">-</button>

                                    <p class="offer-price m-2 mb-0"> {{$item['quantity']}} </p>
                                <button class="btn btn-theme-round btn-number" type="button" wire:click="increase({{$cart_item->id}})">+</button>
                            </div>
                        </div>

                    </div>
                    <div class="d-flex align-items-end">
                        <button class="btn remove-cart" onclick="removeFromCart({{$cart_item->id}})">
                            <i style="font-size: 1rem" class="mdi mdi-delete text-danger"></i>
                        </button>
                    </div>
                </div>

            </div>
        @empty
        @endforelse
    </div>
    <div class="cart-sidebar-footer">
        <div class="cart-store-details">
            <p>@lang('ecommerce::locale.subtotal') <strong
                        class="float-right cart_total_price"> {{$subtotal}}  @lang('ecommerce::locale.pound') </strong></p>
            {{--<p>Delivery Charges <strong class="float-right text-danger">+ $29.69</strong></p>--}}
            {{--<h6>Your total savings <strong class="float-right text-danger">$55 (42.31%)</strong></h6>--}}
        </div>
        <a href="@if($count <= 0) # @else {{route('cart.checkout')}} @endif">
            <button class="btn btn-secondary btn-lg btn-block text-left" type="button" @if($count <= 0) disabled @endif><span
                        class="mdi mdi-cart-outline"></i> @lang('ecommerce::locale.checkout') </span><span
                        class="float-right"><strong
                            class="cart_checkout_total">{{$subtotal}}  @lang('ecommerce::locale.pound')</strong> <span
                            class="mdi mdi-chevron-right"></span></span></button>
        </a>
    </div>

</div>