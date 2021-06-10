<!DOCTYPE html>
<html lang="ar">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Askbootstrap">
    <meta name="author" content="Askbootstrap">
    <title>@lang('ecommerce::locale.company_name') | @yield('title')</title>
    <!-- Favicon Icon -->
    <link rel="icon" type="image/png" href="{{asset('frontend/images/logo.png')}}">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('asset_them/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Material Design Icons -->
    <link href="{{ asset('asset_them/vendor/icons/css/materialdesignicons.min.css')}}" media="all" rel="stylesheet"
          type="text/css"/>
    <!-- Select2 CSS -->
    <link href="{{ asset('asset_them/vendor/select2/css/select2-bootstrap.css')}}"/>
    <link href="{{ asset('asset_them/vendor/select2/css/select2.min.css')}}" rel="stylesheet"/>
    <!-- Custom styles for this template -->
    <link href="{{ asset('asset_them/css/osahan.css')}}" rel="stylesheet">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="{{ asset('asset_them/vendor/owl-carousel/owl.carousel.css')}}">
    <link rel="stylesheet" href="{{ asset('asset_them/vendor/owl-carousel/owl.theme.css')}}">
    <link rel="stylesheet" href="{{ asset('asset_them/css/osahan.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('asset_them/css/custom.css')}}">
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    @livewireStyles
    @yield('css')
    @yield('facebookpixel')

</head>
<body>
@include('ecommerce::frontend.layouts.partials.navbar')
@yield('content')

@include('ecommerce::frontend.layouts.partials.footer')
<div class="cart-sidebar" id="cartDiv">
    <livewire:cartdiv/>
</div>
{{--&text=Hola%21%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Varela%202.--}}
<a href="{{ $whatsapp }}" class="float" target="_blank">
    <i class="fa fa-whatsapp my-float"></i>
</a>
<div>
 <livewire:modal-cart-count/>

<!-- Modal -->
    <div class="modal fade" id="bottomCartModal" tabindex="-1" role="dialog" aria-labelledby="bottomCartModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="bottomCartModalLabel">السلة</h5>
                </div>
                <livewire:modal-cart/>
                <div class="modal-footer border-0 align-items-stretch">
                    <a href="{{route('cart.checkout')}} "
                       class="btn btn-primary checkout-btn"
                       style="flex: 1; border-radius: 0 !important;">@lang('ecommerce::locale.checkout') </a>
                    <button type="button" class="btn close-btn" data-dismiss="modal">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- Bootstrap core JavaScript -->
<script src="{{ asset('asset_them/vendor/jquery/jquery.min.js')}}"></script>
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" ></script>--}}
{{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css"  />--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
    const websiteUrl = @json(url('/ecommerce'));

            @if(Session::has('message'))
    var type = "{{ Session::get('alert-type', 'info') }}";
    switch (type) {
        case 'info':
            toastr.info("{{ Session::get('message') }}");
            break;

        case 'warning':
            toastr.warning("{{ Session::get('message') }}");
            break;

        case 'success':
            toastr.success("{{ Session::get('message') }}");
            break;

        case 'error':
            toastr.error("{{ Session::get('message') }}");
            break;


    }
    @else
    @endif
</script>

<script src="{{ asset('asset_them/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- select2 Js -->
<script src="{{ asset('asset_them/vendor/select2/js/select2.min.js')}}"></script>
<!-- Owl Carousel -->
<script src="{{ asset('asset_them/vendor/owl-carousel/owl.carousel.js')}}"></script>
<!-- Custom -->
<script src="{{ asset('asset_them/js/custom.js')}}"></script>
<script src="{{ asset('/js/cart.js')}}" defer></script>


@livewireScripts
@yield('js')


</body>
</html>
