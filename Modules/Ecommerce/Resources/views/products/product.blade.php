@extends('ecommerce::frontend.layouts.master')
@section('title',trans('ecommerce::locale.product'))
@section('content')

    <section class="pt-3 pb-3 page-info section-padding border-bottom bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <a   href="{{url('/')}}"><strong><span class="mdi mdi-home"></span> @lang('ecommerce::locale.home')</strong>
                    </a> <span class="mdi mdi-chevron-right"></span> <a
                        href="{{url()->previous()}}"> @lang('ecommerce::locale.products')</a>
                    <span class="mdi mdi-chevron-right"></span> <a href="#"> {{$product->name ?? ''}}</a>
                </div>
            </div>
        </div>
    </section>



    <section class="shop-single section-padding pt-3">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="shop-detail-left">
                        <div class="shop-detail-slider">
                            <div id="sync1" class="owl-carousel">

                                @if($product->image != null )
                                    <div class="item"><img src="{{env('POS_URL') . "uploads/img/".$product->image}}"
                                                           alt="{{$product->name ?? ''}}" class="w-100 img-center">
                                    </div>

                                @else
                                    <div class="item"><img src="{{asset('frontend/images/placeholder.png')}}"
                                                           alt="{{$product->name ?? ''}}" class="w-100 img-center">
                                    </div>

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $cart_controller = new \Modules\Ecommerce\Http\Controllers\CartsController();
                    $data_with_discount = $cart_controller->set_discount($product,$product->variations->first()->default_sell_price,true);
                @endphp
                <div class="col-md-6">
                    <div class="shop-detail-right">

                        <span class="badge badge-success">{{$data_with_discount['discount_value']}}{{$data_with_discount['discount_type'] == 'percentage'? '%': __('ecommerce::locale.pound')}} خصم</span>


                    @if($product->variation_location_details && $product->variation_location_details->qty_available > 0)
                            <p class="float-right"><span class="badge badge-success">متاح</span></p>
                        @else
                            <p class="float-right"><span class="badge " style="border: 1px solid #dc3545 !important; color: #dc3545 !important;">غير متاح</span></p>
                        @endif

                        <h2>{{$product->name ?? ''}}</h2>
                        <h6><strong><span class="mdi mdi-approval"></span> @lang('ecommerce::locale.available_in')
                            </strong> {{ $product->unit->actual_name }}</h6>
                        {{--<p class="regular-price"><i class="mdi mdi-tag-outline"></i>{{(double)$product->variations->first()->default_sell_price + ((double)$product->variations->first()->default_sell_price / 10) ?? ''}} @lang('ecommerce::locale.pound')</p>--}}

                        {{--<p class="offer-price mb-0"><span--}}
                                {{--class="text-success">{{(double)$product->variations->first()->default_sell_price ?? ''}} @lang('ecommerce::locale.pound') </span>--}}
                        {{--</p>--}}
                        <h6 class="offer-price mb-0">
                            {{ $data_with_discount['price_after_discount'] ??  (double)$product->variations->first()->default_sell_price ?? ''}} @lang('ecommerce::locale.pound')
                        </h6>
                        <p class="regular-price"><i class="mdi mdi-tag-outline"></i>
                            {{ $data_with_discount['price_after_discount'] & $data_with_discount['price_after_discount'] < (double)$product->variations->first()->default_sell_price ? (double)$product->variations->first()->default_sell_price : ''}}
                            @lang('ecommerce::locale.pound')</p>
                        <a href="#" onclick="addToCart({{$product->id}})">
                            <button type="button" class="btn btn-secondary btn-lg" @if(!($product->variation_location_details && $product->variation_location_details->qty_available > 0)) disabled @endif><i
                                    class="mdi mdi-cart-outline"></i>@lang('ecommerce::locale.add_to_cart')</button>
                        </a>

                        <!-- <div class="short-description">
                        <h5>
                        Quick Overview
                        </h5>
                        <p><b>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</b> Nam fringilla augue nec est tristique auctor. Donec non est at libero vulputate rutrum.
                        </p>
                        <p class="mb-0"> Vivamus adipiscing nisl ut dolor dignissim semper. Nulla luctus malesuada tincidunt. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hiMenaeos. Integer enim purus, posuere at ultricies eu, placerat a felis. Suspendisse aliquet urna pretium eros convallis interdum.</p>
                        </div> -->
                        <!-- <h6 class="mb-3 mt-4">Why shop from Groci?</h6> -->
                        <!-- <div class="row">
                            <div class="col-md-6">
                            <div class="feature-box">
                            <i class="mdi mdi-truck-fast"></i>
                            <h6 class="text-info">Free Delivery</h6>
                            <p>Lorem ipsum dolor...</p>
                            </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-box">
                                    <i class="mdi mdi-basket"></i>
                                    <h6 class="text-info">100% Guarantee</h6>
                                    <p>Rorem Ipsum Dolor sit...</p>
                                </div>
                            </div>
                        </div> -->
                        <div class="d-flex">
                            <div class="d-flex">
                                <span class="input-group-btn"><button  class="btn btn-theme-round btn-number p-2 my-0 mx-1" onclick="changeQuantity('decrease','{{$product->id}}','{{ $product->category_id }}')" type="button">-</button></span>
                                <input style="width: 40px" type="number" value="1" class="form-control border-form-control text-center form-control-sm input-number input_{{ $product->category_id }}_{{$product->id}}" id="input_{{$product->id}}" disabled >
                                <span class="input-group-btn"><button class="btn btn-theme-round btn-number p-2 my-0 mx-1" onclick="changeQuantity('increase', '{{$product->id}}','{{ $product->category_id }}')" type="button">+</button>
                                       </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    {{--@dd($product)--}}
    <section class="product-items-slider section-padding bg-white border-top">
        <div class="container">
            <div class="section-header">
                <h5 class="heading-design-h5">@lang('ecommerce::locale.products_like')<span
                        class="badge badge-primary">{{$product->category && $product->category->name ? $product->category->name : ''}}</span>
                    <a class="float-right text-secondary"  href="{{url('ecommerce/products?category='. $product->category && $product->category->id ? $product->category->id : '')}}">@lang('ecommerce::locale.view_all')</a>
                </h5>
            </div>
            <div class="owl-carousel owl-carousel-featured">
                @forelse($products_like as $product)

                    @php
                        $cart_controller = new \Modules\Ecommerce\Http\Controllers\CartsController();
                        $data_with_discount = $cart_controller->set_discount($product,$product->variations->first()->default_sell_price,true);
                    @endphp
                    <div class="item">
                        <div class="product">
                            <a href="{{url('ecommerce/product',$product->id)}}">
                                <div class="product-header">
                                    <span class="badge badge-success">{{$data_with_discount['discount_value']}}{{$data_with_discount['discount_type'] == 'percentage'? '%': __('ecommerce::locale.pound')}} خصم</span>

                                    @if($product->image != null)

                                        <img class="img-fluid" src="{{env('POS_URL') . "uploads/img/".$product->image}}"
                                             alt="{{$product->name ?? ''}}">
                                    @else
                                        <img class="img-fluid" src="{{asset('frontend/images/placeholder.png')}}"
                                             alt="{{$product->name ?? ''}}">
                                    @endif
                                    <!-- <span
                                        class="veg @if($product->variation_location_details && $product->variation_location_details->qty_available > 0) text-success @else text-danger @endif mdi mdi-circle"></span> -->
                                    @if($product->variation_location_details && $product->variation_location_details->qty_available > 0)
                                        <div class="text-success font-weight-bold">متاح</div>

                                    @else
                                        <div class="text-danger font-weight-bold">غير متاح</div>

                                    @endif

                                    </div>
                                <div class="product-body">
                                    <h5>{{$product->name ?? ''}}</h5>
                                    <h6><strong><span
                                                class="mdi mdi-approval"></span> @lang('ecommerce::locale.available_in')
                                        </strong> {{ $product->unit->actual_name }}</h6>

                                    {{--<h6 class="offer-price mb-0">{{(double)$product->variations->first()->default_sell_price ?? ''}} @lang('ecommerce::locale.pound') </h6>--}}
                                    {{--<p class="regular-price"><i class="mdi mdi-tag-outline"></i>{{(double)$product->variations->first()->default_sell_price + ((double)$product->variations->first()->default_sell_price / 10) ?? ''}} @lang('ecommerce::locale.pound')</p>--}}

                                    <h6 class="offer-price mb-0">
                                        {{ $data_with_discount['price_after_discount'] ??  (double)$product->variations->first()->default_sell_price ?? ''}} @lang('ecommerce::locale.pound')
                                    </h6>
                                    <p class="regular-price"><i class="mdi mdi-tag-outline"></i>
                                        {{ $data_with_discount['price_after_discount'] & $data_with_discount['price_after_discount'] < (double)$product->variations->first()->default_sell_price ? (double)$product->variations->first()->default_sell_price : ''}}
                                        @lang('ecommerce::locale.pound')</p>
                                </div>
                            </a>

                            <div class="product-footer d-flex justify-content-center align-items-center">

                                <div class="d-flex align-items-center">
                                    <div class="input-group">
                                        <span class="input-group-btn"><button  class="btn btn-theme-round btn-number p-2 my-0 mx-1" onclick="changeQuantity('decrease','{{$product->id}}','{{ $product->category_id }}')" type="button">-</button></span>
                                        <input style="width: 40px" type="number" value="1" class="form-control border-form-control text-center form-control-sm input-number input_{{ $product->category_id }}_{{$product->id}}" id="input_{{$product->id}}" disabled >
                                        <span class="input-group-btn"><button class="btn btn-theme-round btn-number p-2 my-0 mx-1" onclick="changeQuantity('increase', '{{$product->id}}','{{ $product->category_id }}')" type="button">+</button>
                                       </span>
                                    </div>

                                </div>
                                <button type="button" class="btn btn-secondary btn-sm float-right"
                                        onclick="addToCart({{$product->id}})"
                                        @if(!($product->variation_location_details && $product->variation_location_details->qty_available > 0)) disabled @endif
                                ><i
                                        class="mdi mdi-cart-outline"></i>
                                        <!-- @lang('ecommerce::locale.add_to_cart') -->
                                    </button>

                                        </div>

                                    </div>
                    </div>

                @empty
                @endforelse


            </div>
        </div>
    </section>

@endsection
