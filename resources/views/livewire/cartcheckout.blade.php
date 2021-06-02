
    <div class="col-md-4">
        <div class="card">
            <h5 class="card-header"> @lang('ecommerce::locale.my_cart')<span
                    class="text-secondary float-right">({{$count}})</span></h5>
            <div class="card-body pt-0 pr-0 pl-0 pb-0">
                @forelse($cart_items as $key => $item)
                    @php
                        $cart_item = \App\Product::find($item['id']);
                    @endphp
                    <div class="cart-list-product">

                        @if($cart_item->image)

                            <img class="img-fluid" src="{{env('POS_URL') . "uploads/img/".$cart_item->image}}"
                                 alt="{{$cart_item->name ?? ''}}">
                        @else
                            <img class="img-fluid" src="{{asset('frontend/images/placeholder.png')}}"
                                 alt="{{$cart_item->name ?? ''}}">
                        @endif

                        {{--<span class="badge badge-success">50% OFF</span>--}}
                        <h5><a href="#">{{$cart_item->name ?? ''}}</a></h5>
                        <h6><strong><span class="mdi mdi-approval"></span> @lang('ecommerce::locale.available_in')
                            </strong> {{ $cart_item->unit->actual_name ?? '' }}</h6>
                        @if(!$item['attributes']['weighted'])
                            <p class="offer-price m-2 mb-0"> {{$item['quantity']}} </p>
                        @else
                            <p class="offer-price m-2 mb-0"> {{$item['quantity'] / 2}} </p>

                        @endif
                    </div>
                @empty
                @endforelse
            </div>
        </div>
        <div class="cart-store-details">
            <!-- <div>
                <p>@lang('ecommerce::locale.subtotal') <strong
                        class="float-right cart_total_price"> {{$subtotal}} </strong> <strong>@lang('ecommerce::locale.pound') </strong></p>
                {{--<p>Delivery Charges <strong class="float-right text-danger">+ $29.69</strong></p>--}}
                {{--<h6>Your total savings <strong class="float-right text-danger">$55 (42.31%)</strong></h6>--}}
            </div> -->
            <div class="d-flex justify-content-between">
                <div>@lang('ecommerce::locale.subtotal') </div>
                <div><strong id="subtotal">{{$subtotal}}</strong><strong>جنيه</strong></div>
            </div>
            <div class="d-flex justify-content-between">
                <div>خدمة التوصيل</div>
                <div><strong id="delivery-cost">{{$delivery_cost}}</strong><strong>جنيه</strong></div>
            </div>
            <div class="d-flex justify-content-between">
                <div>الإجمالي</div>
                <div><strong id="total-cost">{{$total_with_delivery}}</strong><strong>جنيه</strong></div>
            </div>
        </div>
    </div>


<script>

    function updateTotalCost() {
        var inputValue = document.getElementById('delivery_id').value;
        var inputPrice = $(`#address_${inputValue}`).attr('price');
        document.getElementById('delivery-cost').innerText = inputPrice;

        var totalWithDelivery =  (+document.getElementById('subtotal').innerText)+ (+inputPrice);

        document.getElementById('total-cost').innerText = totalWithDelivery;


        if(inputValue !== 'default') {
            document.getElementById('checkout-submit-btn').disabled = false;
        }else {
            document.getElementById('checkout-submit-btn').disabled = true;
        }

        window.livewire.emit('delivery_cost',inputPrice);
    }
</script>