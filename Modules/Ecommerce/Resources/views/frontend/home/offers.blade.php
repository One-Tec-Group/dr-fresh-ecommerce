<section class="product-items-slider section-padding">
    <div class="container">
        <div class="section-header">
            <h5 class="heading-design-h5">العروض
            </h5>
        </div>
        <div class="">
            @forelse($offers as $offer)
            <div class="item">
                <div class="product">
                    <a href="{{url('ecommerce/product',$offer->product->id)}}">
                        <div class="product-header">
                            <span class="badge badge-success">{{ $offer->price_persent }}% خصم</span>

                            @if($offer->product->image != null)

                                <img class="img-fluid" src="{{env('POS_URL') . "uploads/img/".$offer->product->image}}"
                                     alt="{{$offer->product->name ?? ''}}">
                            @else
                                <img class="img-fluid" src="{{asset('frontend/images/placeholder.png')}}"
                                     alt="{{$offer->product->name ?? ''}}">
                            @endif
                            <!-- <span
                                class="veg @if($offer->product->variation_location_details && $offer->product->variation_location_details->qty_available > 0) text-success @else text-danger @endif mdi mdi-circle"></span> -->
                            @if($offer->product->variation_location_details && $offer->product->variation_location_details->qty_available > 0)
                                <div class="text-success font-weight-bold">متاح</div>

                            @else
                                <div class="text-danger font-weight-bold">غير متاح</div>

                            @endif

                            </div>
                        <div class="product-body">
                            <h5>{{$offer->product->name ?? ''}}</h5>
                            <h6><strong><span
                                        class="mdi mdi-approval"></span> @lang('ecommerce::locale.available_in')
                                </strong> {{ $offer->product->unit->actual_name }}</h6>
                                <strong>@lang('ecommerce::locale.offer_product_count') {{ $offer->quantity }} * {{ $offer->product->unit->actual_name }}</strong>

                            <h6 class="offer-price mb-0">{{ ((double)$offer->product->variations->first()->default_sell_price * $offer->quantity) - ($offer->price_persent/100) ?? ''}} @lang('ecommerce::locale.pound') </h6>
                            <p class="regular-price"><i class="mdi mdi-tag-outline"></i>{{(double)$offer->product->variations->first()->default_sell_price * $offer->quantity ?? ''}} @lang('ecommerce::locale.pound')</p>

                        </div>
                    </a>

                    <div class="product-footer d-flex justify-content-center align-items-center">

                        <div class="d-flex align-items-center">
                            <div class="input-group">
                                <span class="input-group-btn"><button  class="btn btn-theme-round btn-number p-2 my-0 mx-1" onclick="changeQuantity('decrease','{{$offer->product->id}}','{{ $offer->product->category_id }}')" type="button">-</button></span>
                                <input style="width: 40px" type="number" value="1" class="form-control border-form-control text-center form-control-sm input-number input_{{ $offer->product->category_id }}_{{$offer->product->id}}" id="offer_input_{{$offer->id}}" disabled >
                                <span class="input-group-btn"><button class="btn btn-theme-round btn-number p-2 my-0 mx-1" onclick="changeQuantity('increase', '{{$offer->product->id}}','{{ $offer->product->category_id }}')" type="button">+</button>
                               </span>
                            </div>

                        </div>
                        <button type="button" class="btn btn-secondary btn-sm float-right"
                                onclick="addOfferToCart({{ $offer->id }})"
                                @if(!($offer->product->variation_location_details && $offer->product->variation_location_details->qty_available > 0)) disabled @endif
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