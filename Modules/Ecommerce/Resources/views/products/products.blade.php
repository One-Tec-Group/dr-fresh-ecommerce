@extends('ecommerce::frontend.layouts.master')
@section('title',trans('ecommerce::locale.products'))

@section('content')

    <section class="pt-3 pb-3 page-info section-padding border-bottom bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <a href="{{url('ecommerce')}}"><strong><span class="mdi mdi-home"></span> @lang('ecommerce::locale.home')</strong>
                    </a> <span class="mdi mdi-chevron-right"></span> <a
                            href="{{ route('products.index') }}"> @lang('ecommerce::locale.products')</a>
                </div>
            </div>
        </div>
    </section>



    <section class="shop-list section-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="shop-filters">
                        <div id="accordion">
                            {{--filters--}}

                            {{--categories--}}
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne"
                                                aria-expanded="true" aria-controls="collapseOne">
                                            @lang('ecommerce::locale.category') <span
                                                    class="mdi mdi-chevron-down float-right"></span>
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                     data-parent="#accordion">
                                    <div class="card-body card-shop-filters">
                                        {{--<form class="form-inline mb-3">--}}
                                            {{--<div class="form-group">--}}
                                                {{--<input type="text" class="form-control"--}}
                                                       {{--placeholder=" @lang('ecommerce::locale.search_by_category') ">--}}
                                            {{--</div>--}}

                                        {{--</form>--}}

                                        @forelse($categories as $category)

                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="categories[]" class="custom-control-input filter_cat"
                                                       id="{{$category->id }}" value="{{$category->id }}"
                                                       @if(request()->has('category') && request('category') == $category->id) checked @endif>
                                                <label class="custom-control-label"
                                                       for="{{$category->id}}">{{$category->name ?? ''}} </label>
                                            </div>

                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{--price--}}
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapseTwo" aria-expanded="false"
                                                aria-controls="collapseTwo">
                                            @lang('ecommerce::locale.price') <span
                                                    class="mdi mdi-chevron-down float-right"></span>
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                     data-parent="#accordion">
                                    <div class="card-body card-shop-filters">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="price" class="custom-control-input" id="price_1to10">
                                            <label class="custom-control-label" for="price_1to10">1 @lang('ecommerce::locale.to')  10</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="price" class="custom-control-input" id="price_10to100">
                                            <label class="custom-control-label" for="price_10to100">10 @lang('ecommerce::locale.to')  100</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="price" class="custom-control-input" id="price_100">
                                            <label class="custom-control-label" for="price_100">100</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{--<div class="left-ad mt-4">--}}
                        {{--<img class="img-fluid" src="http://via.placeholder.com/254x557" alt="">--}}
                    {{--</div>--}}
                </div>
                <div class="col-md-9">
                    <div class="shop-head">
                        <div class="btn-group float-right mt-2">
                            <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                @lang('ecommerce::locale.Sort') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#" id="sort_price_low"> @lang('ecommerce::locale.price') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @lang('ecommerce::locale.low_to_high')
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                <a class="dropdown-item" href="#" id="sort_price_high"> @lang('ecommerce::locale.price') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @lang('ecommerce::locale.high_to_low')
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                <a class="dropdown-item" href="#" id="sort_name"> @lang('ecommerce::locale.name') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @lang('ecommerce::locale.a_to_z')
                                    &nbsp;&nbsp;&nbsp;&nbsp;</a>
                            </div>
                        </div>
                        <h5 class="mb-3"> @lang('ecommerce::locale.products')</h5>
                    </div>


                    <div id="productsDiv">
                        @include('ecommerce::products.products_div')
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('js')

    <script>



        var categories = [];
        var baseUrl = @json(route('products.index'));
        var filterUrl = @json(request('category'));
        var filterSort = null;
        var filterPrice = null;

        if(filterUrl){
            categories.push(filterUrl);
        }



        $('.filter_cat').click(function () {
            if(categories.includes($(this).val())){
                var currentIndex = categories.indexOf($(this).val());
                categories.splice(currentIndex,1);

            }else{
                categories.push($(this).val());
            }

            fetch_data();
        });

        $('#sort_name,#sort_price_high,#sort_price_low').click(function () {
            filterSort = $(this).attr('id');
            fetch_data();
        });

        $('#price_100,#price_10to100,#price_1to10').click(function () {

            filterPrice = $(this).attr('id');
            fetch_data();
        });



        $(document).on('click', '.pagination a', function(event){
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];

            fetch_data(page);
        });




        function fetch_data(page = 1)
        {

            $.ajax({
                url:baseUrl+"?page="+page,
                type:'GET',
                data:{
                    categories: categories,
                    sort: filterSort,
                    price: filterPrice,

                },
                beforeSend:function(){
                    var newUrl = baseUrl +'?page='+page;


                    if(categories != ''){
                        newUrl += '&categories='+categories
                    }
                    if(filterSort != null){
                        newUrl += '&sort='+filterSort
                    }

                    if(filterPrice != null){
                        newUrl += '&price='+filterPrice
                    }

                    history.pushState(null, null, newUrl );

                },
                success:function(data)
                {

                    $('#productsDiv').html(data);
                    $('html, body').animate({ scrollTop: 0 }, 1100);


                }
            });
        }

    </script>

@endsection
