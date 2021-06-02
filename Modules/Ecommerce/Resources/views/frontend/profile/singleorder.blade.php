@extends('ecommerce::frontend.layouts.master')
@section('title',trans('ecommerce::locale.checkout'))
@section('content')

    <section class="pt-3 pb-3 page-info section-padding border-bottom bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <a href="{{url('/ecommerce')}}"><strong><span
                                class="mdi mdi-home"></span> @lang('ecommerce::locale.home')</strong></a> <span
                        class="mdi mdi-chevron-right"></span>
                    <a href="#">@lang('ecommerce::locale.orderdetails')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="checkout-page section-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="checkout-step">
                        <div >
                            
                            
                            <div>
                                <div class="card-header" >
                                    <h5 class="mb-0">
                                        @lang('ecommerce::locale.delivery_address')
                                    </h5>
                                </div>
                                <div  >
                                    <div class="card-body">
                                        <div class="form-row align-items-center">
                                            <div class="col-auto">
                                                <label
                                                    class="sr-only">@lang('ecommerce::locale.phone_number')</label>
                                                <div class="input-group mb-2">
                                                 
                                                    <input type="text" name="phone" class="form-control" disabled
                                                           placeholder="@lang('ecommerce::locale.enter_phone_number')" value="{{ Auth::guard('customer')->user() ? Auth::guard('customer')->user()->phone : '' }}">
                                                </div>
                                            </div>
                                         
                                        </div>
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
                                                        <label for="city">  <i class="fa fa-map-marker"></i> @lang('ecommerce::master.city'):</label>
                                                        <div class="input-group">
                                                            
                                                            <input class="form-control" placeholder="@lang('ecommerce::master.city')" disabled name="city" type="text" id="city" value="{{ !empty($orderlist->addresses->first()) ? $orderlist->addresses->first()->city ?? '' : '' }}">
                                                            <input type="hidden" name="address_id" value="{{ !empty($orderlist->addresses->first()) ? $orderlist->addresses->first()->id ?? '' : '' }}">
                                                            @if ($errors->has('city'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('city') }}</strong>
                                                            </span>
                                                           @endif
                                                           </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="neighborhood"> <i class="fa fa-map-marker"></i> @lang('ecommerce::master.neighborhood'):</label>
                                                        <div class="input-group">
                                                           
                                                         <select class="form-control" name="delivery_id" id="delivery_id" disabled >
                                                                <option value="">@lang('ecommerce::master.no_one')</option>
                                                                @foreach($addresses as $address)
                                                                <option value="{{ $address->id }}" {{ !empty($orderlist->addresses->first()) ?( $orderlist->addresses->first()->delivery_id == $address->id ? 'selected': '') :'' }} >{{ $address->name }}</option>   
                                                                @endforeach                                 
                                                          </select>
                                                          @if ($errors->has('delivery_id'))
                                                          <span class="help-block">
                                                              <strong>{{ $errors->first('delivery_id') }}</strong>
                                                          </span>
                                                         @endif
                                                        </div>
                                                    </div>
                                                </div>
                                          
               
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="street_no"> <i class="fa fa-map-marker"></i>  @lang('ecommerce::master.street'):</label>
                                                        <div class="input-group">
                                                            
                                                            <input class="form-control" placeholder=" @lang('ecommerce::master.street')" disabled name="street_no" type="text" id="street_no" value="{{ !empty($orderlist->addresses->first()) ? $orderlist->addresses->first()->street_no ?? ''  : ''}}">
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
                                                        <label for="building_number"> <i class="fa fa-map-marker"></i>  @lang('ecommerce::master.building'):</label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder=" @lang('ecommerce::master.building')" disabled name="building_number" type="text" id="building_number"  value="{{ !empty($orderlist->addresses->first()) ? $orderlist->addresses->first()->building_number ?? '' : '' }}">
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
                                                        <label for="floor">@lang('ecommerce::master.floor'):  <i class="fa fa-map-marker"></i> </label>
                                                        <div class="input-group">
                                                           
                                                            <input class="form-control" placeholder="@lang('ecommerce::master.floor')" name="floor" disabled type="text" id="floor"  value="{{ !empty($orderlist->addresses->first()) ? $orderlist->addresses->first()->floor ?? '' : '' }}">
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
                                                        <label for="apartment_number">@lang('ecommerce::master.appartment'):  <i class="fa fa-map-marker"></i> </label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="@lang('ecommerce::master.appartment')" disabled name="apartment_number" type="text" id="apartment_number"  value="{{ !empty($orderlist->addresses->first()) ? $orderlist->addresses->first()->apartment_number ?? '' : '' }}">
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
                                                        <label for="special_marque">  <i class="fa fa-map-marker"></i> @lang('ecommerce::master.mark'):</label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="@lang('ecommerce::master.mark')" disabled name="special_marque" type="text" id="special_marque" value="{{ !empty($orderlist->addresses->first()) ? $orderlist->addresses->first()->special_marque ?? '' : '' }}">
                                                            @if ($errors->has('special_marque'))
                                                          <span class="help-block">
                                                              <strong>{{ $errors->first('special_marque') }}</strong>
                                                          </span>
                                                         @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
               
                                           
                                    </div>
                                </div>
                            </div>
                        
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
                <div class="col-md-4">
                    <div class="card">
                        <h5 class="card-header"> @lang('ecommerce::locale.my_cart')<span
                                class="text-secondary float-right"></span></h5>
                        <div class="card-body pt-0 pr-0 pl-0 pb-0">
                            @forelse($orderlist->sell_lines as $key => $item)
                                @php
                                    $cart_item = \App\Product::find($item['product_id']);
                                @endphp
                                <div class="cart-list-product">
            
                                    @if($cart_item->image && file_exists(public_path('uploads/img/'.$cart_item->image)))
            
                                        <img class="img-fluid" src="{{asset('uploads/img/'.$cart_item->image)}}"
                                             alt="{{$cart_item->name ?? ''}}">
                                    @else
                                        <img class="img-fluid" src="{{asset('frontend/images/placeholder.png')}}"
                                             alt="{{$cart_item->name ?? ''}}">
                                    @endif
            
                                    {{--<span class="badge badge-success">50% OFF</span>--}}
                                    <h5><a href="#">{{$cart_item->name ?? ''}}</a></h5>
                                    <h6><strong><span class="mdi mdi-approval"></span> @lang('ecommerce::locale.available_in')
                                        </strong> {{ $cart_item->unit->actual_name ?? '' }}</h6>
                                  
                                        <p class="offer-price m-2 mb-0"> {{$item['quantity']}} </p>
                                 
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                    <div class="cart-store-details">
                        <p>@lang('ecommerce::locale.subtotal') <strong
                                class="float-right cart_total_price"> {{$orderlist->final_total}}  @lang('ecommerce::locale.pound') </strong></p>
                        {{--<p>Delivery Charges <strong class="float-right text-danger">+ $29.69</strong></p>--}}
                        {{--<h6>Your total savings <strong class="float-right text-danger">$55 (42.31%)</strong></h6>--}}
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
