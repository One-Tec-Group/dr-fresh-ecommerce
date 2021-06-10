{{--products div--}}
<div class="row no-gutters">
    @forelse($products as $product)
        @php
            $cart_controller = new \Modules\Ecommerce\Http\Controllers\CartsController();
            $data_with_discount = $cart_controller->set_discount($product,$product->variations->first()->default_sell_price,true);
        @endphp
        <div class="col-md-4">
            <div class="product">
                <a href="{{url('ecommerce/product',$product->id)}}">
                    <div>
                        <div class="product-header">
                            @if($data_with_discount['discount_value'] != 0)

                                <span class="badge badge-success">{{$data_with_discount['discount_value']}}{{$data_with_discount['discount_type'] == 'percentage'? '%': __('ecommerce::locale.pound')}}
                                    خصم</span>
                            @endif
                            @if($product->image != null )

                                <img class="img-fluid" src="{{env('POS_URL') . "uploads/img/".$product->image}}"
                                     alt="{{$product->name ?? ''}}">
                            @else
                                <img class="img-fluid" src="{{asset('frontend/images/placeholder.png')}}"
                                     alt="{{$product->name ?? ''}}">
                            @endif
                            @if($product->qty_available >= 0)
                                <h6 class="text-success font-weight-bold">متاح </h6>
                            @else
                                <h6 class="text-danger font-weight-bold">غير متاح </h6>
                            @endif

                        </div>
                        <div class="product-body">
                            <h5>{{$product->name ?? ''}}</h5>
                            <h6><strong><span class="mdi mdi-approval"></span> @lang('ecommerce::locale.available_in')
                                </strong> {{ $product->actual_name }}</h6>
                            <h6 class="offer-price mb-0">
                                {{ $data_with_discount['price_after_discount'] ??  (double)$product->variations->first()->default_sell_price ?? ''}} @lang('ecommerce::locale.pound')
                            </h6>
                            @if($data_with_discount['discount_value'] != 0)
                                <p class="regular-price"><i class="mdi mdi-tag-outline"></i>
                                    {{ $data_with_discount['price_after_discount'] & $data_with_discount['price_after_discount'] < (double)$product->variations->first()->default_sell_price ? (double)$product->variations->first()->default_sell_price : ''}}
                                    @lang('ecommerce::locale.pound')</p>
                            @endif

                        </div>
                    </div>
                </a>

                <div class="product-footer d-flex justify-content-center align-items-center">

                    <div class="d-flex align-items-center">
                        <div class="input-group" style="flex-wrap: nowrap">
                            <span class="input-group-btn"><button class="btn btn-theme-round btn-number p-2 my-0 mx-1"
                                                                  onclick="changeQuantity('decrease','{{$product->id}}','{{ $product->category_id }}')"
                                                                  type="button">-</button></span>
                            <input style="width: 40px" type="number" value="1"
                                   class="form-control border-form-control text-center form-control-sm input-number input_{{ $product->category_id }}_{{$product->id}}"
                                   id="input_{{$product->id}}" disabled>
                            <span class="input-group-btn"><button class="btn btn-theme-round btn-number p-2 my-0 mx-1"
                                                                  onclick="changeQuantity('increase', '{{$product->id}}','{{ $product->category_id }}')"
                                                                  type="button">+</button>
                                       </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm float-right"
                            onclick="addToCart({{$product->id}})"
                            @if(!($product->qty_available  >= 0))
                            disabled
                            @endif

                    ><i
                                class="mdi mdi-cart-outline"></i>
                    </button>
                </div>
            </div>
        </div>
    @empty
    @endforelse
</div>



@if ($products->hasPages())
    <nav>
        <ul class="pagination justify-content-center mt-4">
            @if ($products->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                </li>
            @else

                <li class="page-item ">
                    <span class="page-link"><a href="{{ $products->previousPageUrl() }}" rel="prev">Previous</a></span>
                </li>
            @endif

            <li class="page-item active">
                           <span class="page-link">
                           {{$products->currentPage()}} @lang('ecommerce::locale.of') {{$products->lastPage()}}
                               <span class="sr-only">(current)</span>
                           </span>
            </li>


            @if ($products->hasMorePages())
                <li class="page-item ">
                    <span class="page-link"><a href="{{ $products->nextPageUrl() }}" rel="prev">Next</a></span>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
