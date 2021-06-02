
{{--<div class="cart-sidebar-header">--}}
    {{--<h5>--}}
        {{--@lang('ecommerce::locale.my_cart') <span class="text-success">({{\Cart::getContent()->count()}})</span> <a id="offcanvas" data-toggle="offcanvas" class="float-right" href="#"><i class="mdi mdi-close"></i>--}}
        {{--</a>--}}
    {{--</h5>--}}
{{--</div>--}}
{{--<div class="cart-sidebar-body">--}}
    {{--@forelse(\Cart::getContent() as $item)--}}
        {{--@php--}}
            {{--$cart_item = \App\Product::find($item->id);--}}
        {{--@endphp--}}
        {{--<div class="cart-list-product">--}}
            {{--<a class="float-right remove-cart" onclick="removeFromCart({{$item->id}})" href="#"><i class="mdi mdi-close"></i></a>--}}
            {{--@if($item->image != null && file_exists(base_path('uploads/img/'.$item->image)))--}}

                {{--<img class="img-fluid" src="{{asset('uploads/img/'.$item->image)}}"--}}
                     {{--alt="{{$item->name ?? ''}}">--}}
            {{--@else--}}
                {{--<img class="img-fluid" src="{{asset('frontend/images/placeholder.png')}}"--}}
                     {{--alt="{{$item->name ?? ''}}">--}}
            {{--@endif--}}
            {{--<span class="badge badge-success">50% OFF</span>--}}
            {{--<h5>{{$item->name ?? ''}}</h5>--}}
            {{--<h6><strong><span class="mdi mdi-approval"></span> @lang('ecommerce::locale.available_in')--}}
                {{--</strong> {{ $cart_item->unit->actual_name }}</h6>--}}
            {{--<p class="offer-price mb-0">{{(double)$cart_item->variations->first()->default_sell_price ?? ''}} @lang('ecommerce::locale.pound') </p>--}}
        {{--</div>--}}
    {{--@empty--}}
    {{--@endforelse--}}
{{--</div>--}}
{{--<div class="cart-sidebar-footer">--}}
    {{--<div class="cart-store-details">--}}
        {{--<p>Sub Total <strong class="float-right cart_total_price">$0</strong></p>--}}
        {{-- <p>Delivery Charges <strong class="float-right text-danger">+ $29.69</strong></p>--}}
        {{--<h6>Your total savings <strong class="float-right text-danger">$55 (42.31%)</strong></h6> --}}
    {{--</div>--}}
    {{--<a href="#"><button class="btn btn-secondary btn-lg btn-block text-left" type="button"><span class="float-left"><i class="mdi mdi-cart-outline"></i> Proceed to Checkout </span><span class="float-right"><strong class="cart_checkout_total">$0</strong> <span class="mdi mdi-chevron-right"></span></span></button></a>--}}
{{--</div>--}}
