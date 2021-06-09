@extends('ecommerce::frontend.layouts.master')
@section('title',trans('ecommerce::locale.home'))
@section('facebookpixel')
<!-- Facebook Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '226602388937605');
        fbq('track', 'PageView');
        //handles what you need from even can be changed


        // end handles
    </script>
    <noscript>
        <img height="1" width="1"
             src="https://www.facebook.com/tr?id=226602388937605&ev=PageView
&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
@endsection
@section('content')

    <div class="social-links">
        <div class="social-link">
            <a href="{{ $settings->where('key', 'facebook')->first()->value }}" target="_blank">
                <img class="w-100" src="{{asset('frontend/images/facebook.svg')}}" alt="facebook logo">
            </a>
        </div>
        <div class="social-link">
            <a href="{{ $settings->where('key', 'instagram')->first()->value }}" target="_blank">
                <img class="w-100" src="{{asset('frontend/images/instagram.svg')}}" alt="instagram logo">
            </a>
        </div>
        <div class="social-link">
            <a href="{{ $settings->where('key', 'twitter')->first()->value }}" target="_blank">
                <img class="w-100" src="{{asset('frontend/images/telephone.svg')}}" alt="instagram logo">
            </a>
        </div>
    </div>

    <section class="carousel-slider-main text-center border-top border-bottom bg-white">
        <div class="owl-carousel owl-carousel-slider">
            @foreach ($sliders as $slider)
                <div class="item">
                    <a href="{{ $slider->link }}">
                        <img class="w-100 img-fluid" src="{{ env('POS_URL').$slider->dir }}"
                                            alt="First slide">
                    </a>
                </div>
            @endforeach
           
            {{-- <div class="item">
                <a href="#"><img class="w-100 img-fluid" src="{{ asset('frontend/images/slider 2.png')}}"
                                         alt="First slide"></a>
            </div> --}}
            {{--<div class="item">--}}
                {{--<a href="#"><img class="w-100 img-fluid" src="{{ asset('frontend/images/home-hero.png')}}"--}}
                                         {{--alt="First slide"></a>--}}
            {{--</div>--}}
            {{--<div class="item">--}}
                {{--<a href="#"><img class="w-100 img-fluid" src="{{ asset('frontend/images/home-hero.png')}}"--}}
                                         {{--alt="First slide"></a>--}}
            {{--</div>--}}
        </div>
    </section>

    <section class="top-category">
        <div class="container">
            <div class="row">
                @forelse($categories as $category)
                    <div class="col-4 col-lg-2 mb-3">
                        <div class="category-item">
                            <a href="{{url('ecommerce/products?category='.$category->id)}}">
                                @if($category->image != null )

                                    <img class="" src="{{env('POS_URL') . "uploads/categories/".$category->image}}"
                                        alt="{{$category->name ?? ''}}">
                                @else
                                    <img class="" src="{{asset('frontend/images/placeholder.png')}}"
                                        alt="{{$category->name ?? ''}}">
                                @endif
                                <h6>{{ $category->name ?? '' }}</h6>
                                <p>{{$category->products && $category->products->count() ? $category->products->count() : 0 }} @lang('ecommerce::locale.item') </p>
                            </a>
                        </div>
                    </div>
                @empty
                    @endforelse
            </div>
        </div>
    </section>

    @if($offers->count() > 0)
        @include("ecommerce::frontend.home.offers")
    @endif
    
    @forelse($categories as $category)
        <section class="product-items-slider section-padding">
            <div class="container">
                <div class="section-header">
                    <h5 class="heading-design-h5">{{$category->name ?? ''}}
                        <!-- <a class="float-right text-secondary"
                           href="{{url('ecommerce/products?category='.$category->id)}}"> @lang('ecommerce::locale.view_all') </a> -->
                    </h5>
                </div>
                <div class="owl-carousel owl-carousel-featured">
                    @forelse($category->products->take(12) as $product)
                        @php
                            $cart_controller = new \Modules\Ecommerce\Http\Controllers\CartsController();
                            $data_with_discount = $cart_controller->set_discount($product,$product->variations->first()->default_sell_price,true);
                        @endphp
                        <div class="item">
                            <div class="product">
                                <a href="{{url('ecommerce/product/'.$product->id)}}">
                                    <div>
                                        <div class="product-header">
                                            <span class="badge badge-success">{{$data_with_discount['discount_value']}}{{$data_with_discount['discount_type'] == 'percentage'? '%': __('ecommerce::locale.pound')}} خصم</span>
                                            @if($product->image != null )

                                                <img class="img-fluid"  src="{{env('POS_URL') . "uploads/img/".$product->image}}"
                                                     alt="{{$product->name ?? ''}}">
                                            @else
                                                <img class="img-fluid"
                                                     src="{{asset('frontend/images/placeholder.png')}}"
                                                     alt="{{$product->name ?? ''}}">
                                            @endif
                                            @if($product->variation_location_details && $product->variation_location_details->qty_available > 0)
                                            <h6 class="text-success font-weight-bold">متاح </h6>
                                            @else
                                                <h6 class="text-danger font-weight-bold">غير متاح </h6>
                                                 @endif
                                            <!-- <span class="veg @if($product->variation_location_details && $product->variation_location_details->qty_available > 0) text-success @else text-danger @endif mdi mdi-circle"></span> -->
                                        </div>
                                        <div class="product-body">
                                            <h5>{{$product->name ?? ''}}</h5>
                                            <h6><strong><span
                                                        class="mdi mdi-approval"></span> @lang('ecommerce::locale.available_in')
                                                </strong> {{ $product->unit->actual_name }}</h6>
                                            <h6 class="offer-price mb-0">
                                                {{ $data_with_discount['price_after_discount'] ??  (double)$product->variations->first()->default_sell_price ?? ''}} @lang('ecommerce::locale.pound')
                                            </h6>
                                            <p class="regular-price"><i class="mdi mdi-tag-outline"></i>
                                                {{ $data_with_discount['price_after_discount'] & $data_with_discount['price_after_discount'] < (double)$product->variations->first()->default_sell_price ? (double)$product->variations->first()->default_sell_price : ''}}
                                                @lang('ecommerce::locale.pound')</p>

                                        </div>
                                    </div>
                                </a>

                                <div class="product-footer d-flex justify-content-center align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="input-group" style="flex-wrap: nowrap">
                                        <span class="input-group-btn"><button  class="btn btn-theme-round btn-number p-2 my-0 mx-1" onclick="changeQuantity('decrease', '{{$product->id}}','{{ $category->id }}')" type="button">-</button></span>
                                        <input style="width: 40px" type="number" value="1" class="form-control border-form-control text-center form-control-sm input-number input_{{ $category->id }}_{{$product->id}}" id="input_{{$product->id}}" disabled >
                                        <span class="input-group-btn"><button class="btn btn-theme-round btn-number p-2 my-0 mx-1" onclick="changeQuantity('increase', '{{$product->id}}','{{ $category->id }}')" type="button">+</button>
                                       </span>
                                    </div>
                                </div>
                                    <button type="button" class="btn btn-secondary btn-sm float-right"
                                            onclick="addToCart({{$product->id}})" @if($product->variation_location_details && $product->variation_location_details->qty_available <= 0) disabled @endif><i
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
    @empty
    @endforelse
    
@endsection


