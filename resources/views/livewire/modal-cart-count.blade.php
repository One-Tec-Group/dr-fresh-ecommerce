<div>
    @if(true)

        <button class="btn cart-btn-fixed" data-toggle="modal" data-target="#bottomCartModal">
            <img src="{{ asset('asset_them/img/shopping-cart.svg') }}" alt="">
            <span style="position: absolute;
    top: -6px;
    left: -6px;
    background: #387038;
    font-size: 13px;
    border-radius: 50%;
    height: 20px;
    width: 20px;">{{$count ?? ''}}</span>
        </button>
    @endif
</div>
