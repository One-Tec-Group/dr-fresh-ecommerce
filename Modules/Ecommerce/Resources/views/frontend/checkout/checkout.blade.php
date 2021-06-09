@extends('ecommerce::frontend.layouts.master')
@section('title',trans('ecommerce::locale.checkout'))
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

                fbq('track', 'InitiateCheckout');


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

    <section class="pt-3 pb-3 page-info section-padding border-bottom bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <a href="{{url('/ecommerce')}}"><strong><span
                                    class="mdi mdi-home"></span> @lang('ecommerce::locale.home')</strong></a> <span
                            class="mdi mdi-chevron-right"></span>
                    <a href="#">@lang('ecommerce::locale.checkout')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="checkout-page section-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="checkout-step">
                        <div class="accordion" id="accordionExample">
                            <form action="{{ route('cart.checkoutStore') }}" method="post">
                                @csrf
                                <input type="hidden" name="coupon_discount" id="coupon_discount_store" value="0"/>
                                <div class="card checkout-step-one">
                                    <div class="card-header" id="headingOne">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" type="button" data-toggle="collapse"
                                                    data-target="#collapseOne" aria-expanded="true"
                                                    aria-controls="collapseOne">
                                                <span class="number">1</span> @lang('ecommerce::locale.phone_verification')
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                         data-parent="#accordionExample">
                                        <div class="card-body">
                                            {{--<p>We need your phone number so that we can update you about your order.</p>--}}

                                            <div class="form-row align-items-center">
                                                <div class="col-auto">
                                                    <label
                                                            class="sr-only">@lang('ecommerce::locale.phone_number')</label>
                                                    <div class="input-group mb-2">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text"><span
                                                                        class="mdi mdi-cellphone-iphone"></span></div>
                                                        </div>
                                                        <input type="text" name="phone" class="form-control"
                                                               placeholder="@lang('ecommerce::locale.enter_phone_number')"
                                                               value="{{ Auth::guard('customer')->user() ? Auth::guard('customer')->user()->phone : '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" data-toggle="collapse"
                                                            data-target="#collapseTwo" aria-expanded="false"
                                                            aria-controls="collapseTwo"
                                                            class="btn btn-secondary mb-2 btn-lg">@lang('ecommerce::locale.next')
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="card checkout-step-two">
                                    <div class="card-header" id="headingTwo">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                                    data-target="#collapseTwo" aria-expanded="false"
                                                    aria-controls="collapseTwo">
                                                <span class="number">2</span> @lang('ecommerce::locale.delivery_address')
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                         data-parent="#accordionExample">
                                        <div class="card-body">

                                            <div class="row">

                                                <div class="clearfix"></div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="Address">@lang('ecommerce::master.address')</label>

                                                    </div>
                                                </div>

                                                <div class="clearfix"></div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="city"> <i
                                                                    class="fa fa-map-marker"></i> @lang('ecommerce::master.city')
                                                            :</label>
                                                        <div class="input-group">

                                                            <input class="form-control"
                                                                   placeholder="@lang('ecommerce::master.city')"
                                                                   name="city" type="text" id="city"
                                                                   value="6 أكتوبر">
                                                            <input type="hidden" name="address_id"
                                                                   value="{{ $current_address->id ?? '' }}">
                                                            @if ($errors->has('city'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('city') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <livewire:delivery-group-input/>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="street_no"> <i
                                                                    class="fa fa-map-marker"></i> @lang('ecommerce::master.street')
                                                            :</label>
                                                        <div class="input-group">

                                                            <input class="form-control"
                                                                   placeholder=" @lang('ecommerce::master.street')"
                                                                   name="street_no" type="text" id="street_no"
                                                                   value="{{ $current_address->street_no ?? '' }}">
                                                            @if ($errors->has('street_no'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('street_no') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="building_number"> <i
                                                                    class="fa fa-map-marker"></i> @lang('ecommerce::master.building')
                                                            :</label>
                                                        <div class="input-group">
                                                            <input class="form-control"
                                                                   placeholder=" @lang('ecommerce::master.building')"
                                                                   name="building_number" type="text"
                                                                   id="building_number"
                                                                   value="{{ $current_address->building_number ?? '' }}">
                                                            @if ($errors->has('building_number'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('building_number') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="floor">@lang('ecommerce::master.floor'): <i
                                                                    class="fa fa-map-marker"></i> </label>
                                                        <div class="input-group">

                                                            <input class="form-control"
                                                                   placeholder="@lang('ecommerce::master.floor')"
                                                                   name="floor" type="text" id="floor"
                                                                   value="{{ $current_address->floor ?? '' }}">
                                                            @if ($errors->has('floor'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('floor') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="apartment_number">@lang('ecommerce::master.appartment')
                                                            : <i class="fa fa-map-marker"></i> </label>
                                                        <div class="input-group">
                                                            <input class="form-control"
                                                                   placeholder="@lang('ecommerce::master.appartment')"
                                                                   name="apartment_number" type="text"
                                                                   id="apartment_number"
                                                                   value="{{ $current_address->apartment_number ?? '' }}">
                                                            @if ($errors->has('apartment_number'))
                                                                <span class="help-block">
                                                              <strong>{{ $errors->first('apartment_number') }}</strong>
                                                          </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="special_marque"> <i
                                                                    class="fa fa-map-marker"></i> @lang('ecommerce::master.mark')
                                                            :</label>
                                                        <div class="input-group">
                                                            <input class="form-control"
                                                                   placeholder="@lang('ecommerce::master.mark')"
                                                                   name="special_marque" type="text" id="special_marque"
                                                                   value="{{ $current_address->special_marque ?? '' }}">
                                                            @if ($errors->has('special_marque'))
                                                                <span class="help-block">
                                                              <strong>{{ $errors->first('special_marque') }}</strong>
                                                          </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit"
                                                    id="checkout-submit-btn"
                                                    disabled
                                                    class="btn btn-secondary mb-2 btn-lg">@lang('ecommerce::locale.order_complete')
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </form>
                            {{-- <div class="card">
                                <div class="card-header" id="headingThree">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                                data-target="#collapsefour" aria-expanded="false"
                                                aria-controls="collapsefour">
                                            <span class="number">3</span> @lang('ecommerce::locale.order_complete')
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapsefour" class="collapse" aria-labelledby="headingThree"
                                     data-parent="#accordionExample">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="col-lg-10 col-md-10 mx-auto order-done">
                                                <i class="mdi mdi-check-circle-outline text-secondary"></i>
                                                <h4 class="text-success">@lang('ecommerce::locale.order_accepted')</h4>
                                                <p>
                                                    @lang('ecommerce::locale.order_terms')
                                                </p>
                                            </div>
                                            <div class="text-center">

                                                <a href="{{url('/ecommerce')}}">
                                                    <button type="submit"
                                                            class="btn btn-secondary mb-2 btn-lg">@lang('ecommerce::locale.back_to_shopping')
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <livewire:cartcheckout/>
            </div>
        </div>
    </section>


@endsection
