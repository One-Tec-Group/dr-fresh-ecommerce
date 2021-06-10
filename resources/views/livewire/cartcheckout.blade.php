
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
                        <p class="offer-price m-2 mb-0"> {{$item['quantity']}} </p>

                    </div>
                @empty
                @endforelse
            </div>
        </div>
        <div class="cart-store-details">

            <div class="d-flex justify-content-between">
                <div>@lang('ecommerce::locale.subtotal') </div>
                <div><strong id="subtotal">{{$subtotal}}</strong><strong>جنيه</strong></div>
            </div>
            <div class="d-flex justify-content-between">
                <div>خدمة التوصيل</div>
                <div><strong id="delivery-cost">{{$delivery_cost}}</strong><strong>جنيه</strong></div>
            </div>
            <div class="d-flex justify-content-between" id="coupon_div">
                <div>كوبون خصم(<span id="coupon_num"> </span>) </div>
                <div><strong id="coupon_discount">{{$coupon_discount}}</strong><strong>جنيه</strong></div>
            </div>
            <div class="d-flex justify-content-between">
                <div>الإجمالي</div>
                <div><strong id="total-cost">{{$total_with_delivery}}</strong><strong>جنيه</strong></div>
            </div>
        </div>

        <div class="card checkout-step-one">

            <div>
                <div class="card-body">
                    <div class="form-row align-items-center">
                        <div class="col-auto">
                            <label
                                    class="sr-only">@lang('ecommerce::locale.coupon')</label>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><span
                                                class="mdi mdi-ticket"></span></div>
                                </div>
                                <input type="text" name="coupon" class="form-control" id="coupon"
                                       placeholder="@lang('ecommerce::locale.enter_coupon')"
                                       >
                            </div>
                            <div id="coupon_error" style="display:none; color: red">

                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="button" data-toggle="collapse"
                                    data-target="#collapseTwo" aria-expanded="false"
                                    aria-controls="collapseTwo"
                                    class="btn btn-secondary mb-2 btn-lg" onclick="addCoupon()">@lang('ecommerce::locale.submit_coupon')
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


<script>

    function updateTotalCost() {
        var inputValue = document.getElementById('delivery_id').value;
        var inputPrice = $(`#address_${inputValue}`).attr('price');
        document.getElementById('delivery-cost').innerText = inputPrice;

        var totalWithDelivery =  (+document.getElementById('subtotal').innerText)+ (+inputPrice);
        totalWithDelivery = totalWithDelivery - document.getElementById('coupon_discount_store').value;
        document.getElementById('total-cost').innerText = totalWithDelivery;

        if(inputValue !== 'default') {
            document.getElementById('checkout-submit-btn').disabled = false;
        }else {
            document.getElementById('checkout-submit-btn').disabled = true;
        }

        window.livewire.emit('delivery_cost',inputPrice);
    }

    function addCoupon(){
        var coupon = $('#coupon').val();
        var url = @json(route('ecommerce.coupon'));

        var coupon_error = document.getElementById('coupon_error');
        var coupon_div = document.getElementById('coupon_div');



        var coupon_discount_store = document.getElementById('coupon_discount_store');

        var inputValue = document.getElementById('delivery_id').value;
        var inputPrice = $(`#address_${inputValue}`).attr('price');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: url,
            type:"POST",
            data:{
                coupon:coupon,
            },
            success:function(response){

                var coupon_discount = document.getElementById('coupon_discount');
                var coupon_num = document.getElementById('coupon_num');
                if (response.coupon_discount) {
                    if (coupon_error.style.display == 'block'){

                         coupon_error.style.display  = 'none';
                    }

                    coupon_div.style.display    = 'block';
                    coupon_discount.innerHTML   = response.coupon_discount;
                    coupon_num.innerHTML        = coupon;

                    var totalWithcoupon =  parseFloat((+document.getElementById('subtotal').innerText)+ (+inputPrice) + (-response.coupon_discount)).toFixed(2);
                    document.getElementById('total-cost').innerText = totalWithcoupon;
                    coupon_discount_store.value = response.coupon_discount;
                }

                window.livewire.emit('add_coupon');
            },
            error: function(xhr, status, error){

                coupon_error.innerHTML      = xhr.responseJSON.message;
                if (coupon_div.style.display == 'block'){

                    $('#coupon_div').attr("style", "display: none !important");
                    var totalWithcoupon =  (+document.getElementById('subtotal').innerText) + (+inputPrice);
                    document.getElementById('total-cost').innerText = totalWithcoupon;
                    coupon_discount_store.value = 0;
                }
                coupon_error.style.display    = 'block';

            }
        });
    }
</script>