@include('ecommerce::frontend.layouts.partials.login_modal')
@php

      $categories = \App\Category::where('business_id',config('constants.business_id'))->with('products')->get();
@endphp

{{--copoun div--}}
 {{--<div class="navbar-top bg-success pt-2 pb-2">--}}
    {{--<div class="container-fluid">--}}
       {{--<div class="row">--}}
          {{--<div class="col-lg-12 text-center">--}}
             {{--<a href="shop.html" class="mb-0 text-white">--}}
             {{--20% cashback for new users | Code: <strong><span class="text-light">OGOFERS13 <span class="mdi mdi-tag-faces"></span></span> </strong>--}}
             {{--</a>--}}
          {{--</div>--}}
       {{--</div>--}}
    {{--</div>--}}
 {{--</div>--}}

<!-- .modal-backdrop -->
<div class="sidebar">
<div class="backdrop" onclick="toggleSideDrawer()" id="backdrop" style="display: none;"></div>
<div class="side-drawer" id="side-drawer" style="transform: translateX(-100%);">
      <div class="side-drawer__header">
        <!-- <div class="side-drawer__header__logo">
                    <img src="./images/gm.png" alt="company logo">
                </div> -->
      </div>
      <nav>
        <ul class="side-drawer__items">
          <li class="side-drawer__item">
            <a class="side-drawer__link" href="">الرئيسية</a>
          </li>
          <li class="side-drawer__item">
            <a class="side-drawer__link" href="">المنتجات</a>
          </li>
          <li class="side-drawer__item">
            <a class="side-drawer__link" href="">من نحن</a>
          </li>
          <li class="side-drawer__item">
            <a class="side-drawer__link" href="">أقسام</a>
          </li>
          <li class="side-drawer__item">
            <a class="side-drawer__link" href="">تواصل معنا</a>
          </li>
        </ul>




      </nav>
      <div class="side-drawer__footer py-3">
         <div>تم التطوير بواسطة <a href="https://onetecgroup.com/">One Tec Group</a></div>
      </div>
    </div>
</div>

 <nav class="navbar navbar-light navbar-expand-lg bg-faded osahan-menu">
    <div class="container-fluid">
       <a class="navbar-brand d-flex" href="{{url('ecommerce')}}"> <img  class="w-75" src="{{asset('frontend/images/logo.png')}}" alt="logo"> </a>
       {{--<a class="location-top" href="#"><i class="mdi mdi-map-marker-circle" aria-hidden="true"></i> New York</a>--}}
       <button class="navbar-toggler navbar-toggler-white" type="button" onclick="toggleSideDrawer()">
       <span class="navbar-toggler-icon"></span>
       </button>
       <div class="navbar-collapse" id="navbarNavDropdown">
          <div class="navbar-nav mr-auto mt-2 mt-lg-0 margin-auto top-categories-search-main">
             <form method="GET" action="{{route('products.index')}}">

                <div class="top-categories-search">
                   <div class="input-group">
                      <input class="form-control" name="product_name" value="{{request('product_name') ?? ''}}" placeholder="@lang('ecommerce::locale.search_products') " aria-label="@lang('ecommerce::locale.search_products')" type="text">
                      <span class="input-group-btn">
                      <button class="btn btn-secondary" type="submit"><i class="mdi mdi-file-find"></i> @lang('ecommerce::locale.search')</button>
                      </span>
                   </div>
                </div>
             </form>
          </div>
          <div class="my-2 my-lg-0">
               <ul class="list-inline main-nav-right">
                  
                  
                  @auth('customer')
                  <li class="list-inline-item dropdown osahan-top-dropdown">
                     <a class="btn btn-theme-round dropdown-toggle dropdown-toggle-top-user" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <img alt="logo" src="{{ asset('asset_them/img/user.jpg') }}"><strong>@lang('ecommerce::master.hello')</strong> {{ Auth::guard('customer')->check()?Auth::guard('customer')->user()->first_name:'' }}
                     </a>
                     <div class="dropdown-menu dropdown-menu-right dropdown-list-design">
                        <a href="{{ route('customer.profile') }}" class="dropdown-item"><i aria-hidden="true" class="mdi mdi-account-outline"></i>  @lang('ecommerce::master.My_Profile')</a>
                        <a href="{{ route('customer.address') }}" class="dropdown-item"><i aria-hidden="true" class="mdi mdi-map-marker-circle"></i> @lang('ecommerce::master.My_Address')</a>
                        <a href="{{ route('customer.orderlist') }}" class="dropdown-item"><i aria-hidden="true" class="mdi mdi-format-list-bulleted"></i> @lang('ecommerce::master.Order_List')</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('customer.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" ><i class="mdi mdi-lock"></i> @lang('ecommerce::master.logout')</a>	
                     </div>
                  </li>
                  <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                     {{ csrf_field() }}
                 </form>
                  @endauth
                  @guest('customer')
                  <li class="list-inline-item">
                       
                   <a href="#" data-target="#bd-example-modal" data-toggle="modal" class="btn btn-link"><i class="mdi mdi-account-circle"></i> @lang('ecommerce::master.login_register')</a>
                  </li>
                   @endguest
                <li class="list-inline-item cart-btn" >
                    <livewire:navcart />
                </li>
             </ul>
          </div>
       </div>
    </div>
 </nav>

 <nav class="navbar navbar-expand-lg navbar-light osahan-menu-2 pad-none-mobile">
    <div class="container-fluid">
       <div class="collapse navbar-collapse" id="navbarText">
          <ul class="navbar-nav mr-auto mt-2 mt-lg-0 margin-auto">
             <li class="nav-item">
                <a href="{{route('ecommerce.home')}}" class="nav-link @if(Request::is('ecommerce')) shop @endif">@lang('ecommerce::locale.home')</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link @if(Request::is('ecommerce/products/*') || Request::is('ecommerce/products')) shop @endif" href="{{route('products.index')}}"><span class="mdi mdi-store"></span>@lang('ecommerce::locale.products')</a>
               </li>
             {{-- <li class="nav-item">
                <a href="#" class="nav-link">@lang('ecommerce::locale.about')</a>
             </li> --}}
             {{--<li class="nav-item">--}}
                {{--<a class="nav-link" href="shop.html">Fruits & Vegetables</a>--}}
             {{--</li>--}}

             {{--<li class="nav-item dropdown">--}}
                {{--<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                {{--Pages--}}
                {{--</a>--}}
                {{--<div class="dropdown-menu">--}}
                   {{--<a class="dropdown-item" href="shop.html"><i class="mdi mdi-chevron-right" aria-hidden="true"></i> Shop Grid</a>--}}
                   {{--<a class="dropdown-item" href="single.html"><i class="mdi mdi-chevron-right" aria-hidden="true"></i> Single Product</a>--}}
                   {{--<a class="dropdown-item" href="cart.html"><i class="mdi mdi-chevron-right" aria-hidden="true"></i> Shopping Cart</a>--}}
                   {{--<a class="dropdown-item" href="checkout.html"><i class="mdi mdi-chevron-right" aria-hidden="true"></i> Checkout</a> --}}
                {{--</div>--}}
             {{--</li>--}}
             <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   @lang('ecommerce::locale.categories')
                </a>
                <div class="dropdown-menu">
                   @forelse($categories as $category)
                        <a class="dropdown-item" href="{{url('ecommerce/products?category='.$category->id)}}"><i class="mdi mdi-chevron-right" aria-hidden="true"></i> {{$category->name ?? ''}}</a>
                   @empty
                      <a class="dropdown-item" href="#"><i class="mdi mdi-chevron-right" aria-hidden="true"></i> @lang('ecommerce::locale.categories_not_found')</a>
                   @endforelse

                </div>
             </li>

             <li class="nav-item">
                <a class="nav-link @if(Request::is('contact')) shop @endif " href="{{ route('customer.contact') }}">@lang('ecommerce::locale.contact')</a>
             </li>
          </ul>
       </div>
    </div>
 </nav>
 <script>
        var backrop = document.getElementById("backdrop");
        var sideDrawer = document.getElementById("side-drawer");
        var body = document.getElementsByTagName('body')[0];
        
        function toggleSideDrawer() {
            if (backrop.style.display == "none") {
                console.log(body);
                backrop.style.display = "block";
                sideDrawer.style.transform = "translateX(0px)";
                body.style.overflow = "hidden";
            } else {
                console.log(body);
                backrop.style.display = "none";
                sideDrawer.style.transform = "translateX(-100%)";
                body.style.overflow = "auto";
            }
        }
    </script>