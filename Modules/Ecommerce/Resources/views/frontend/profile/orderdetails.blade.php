@extends('ecommerce::frontend.layouts.master')
@section('title',trans('ecommerce::locale.product'))
@section('content')


<section class="account-page section-padding">
    <div class="container">
       <div class="row">
          <div class="col-lg-9 mx-auto">
             <div class="row no-gutters">
                <div class="col-md-4">
                   <div class="card account-left">
                      <div class="user-profile-header">
                         {{-- <img alt="logo" src="{{asset('asset_them/img/user.jpg')}}"> --}}
                         <h5 class="mb-1 text-secondary"><strong>@lang('ecommerce::master.hello') </strong> {{ Auth::guard('customer')->check()?Auth::guard('customer')->user()->first_name:'' }}</h5>
                         <p> {{ Auth::guard('customer')->check()?Auth::guard('customer')->user()->phone:'' }}</p>
                      </div>
                      <div class="list-group">
                         <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action "><i aria-hidden="true" class="mdi mdi-account-outline"></i>  @lang('ecommerce::master.My_Profile')</a>
                         <a href="{{ route('customer.address') }}" class="list-group-item list-group-item-action "><i aria-hidden="true" class="mdi mdi-map-marker-circle"></i>  @lang('ecommerce::master.My_Address')</a>
                         <a href="{{ route('customer.orderlist') }}" class="list-group-item list-group-item-action active"><i aria-hidden="true" class="mdi mdi-format-list-bulleted"></i>@lang('ecommerce::master.Order_List')</a> 
                         <a  href="{{ route('customer.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="list-group-item list-group-item-action"><i aria-hidden="true" class="mdi mdi-lock"></i>@lang('ecommerce::master.logout')</a> 
                         
                        <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                      </div>
                   </div>
                </div>
                <div class="col-md-8">
                    <div class="card card-body account-right">
                       <div class="widget">
                          <div class="section-header">
                             <h5 class="heading-design-h5">
                               @lang('ecommerce::master.order_list')
                             </h5>
                          </div>
                          <div class="order-list-tabel-main table-responsive">
                             <table class="datatabel table table-striped table-bordered order-list-tabel" width="100%" cellspacing="0">
                                <thead>
                                   <tr>
                                      <th>@lang('ecommerce::master.order') #</th>
                                      <th>@lang('ecommerce::master.data_purchesed')</th>
                                      {{-- <th>@lang('ecommerce::master.status')</th> --}}
                                      <th>@lang('ecommerce::master.total')</th>
                                      <th>@lang('ecommerce::master.action')</th>
                                   </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderlist as $order)
                                   
                                   <tr>
                                      <td>#{{ $order->id }}</td>
                                      <td>{{\Carbon\Carbon::parse( $order->created_at)->isoFormat('dddd,Do MMMM  YYYY') }}</td>
                                      {{-- <td><span class="badge badge-danger">{{ $order->status }}</span></td> --}}
                                      <td>{{ $order->final_total }}</td>
                                      <td><a data-toggle="tooltip" data-placement="top" title="" href="{{ route('customer.orderdetails',['id'=>$order->id]) }}" data-original-title="View Detail" class="btn btn-info btn-sm"><i class="mdi mdi-eye"></i></a></td>
                                   </tr>
                                 @endforeach
                                </tbody>
                             </table>
                          </div>
                       </div>
                    </div>
                 </div>
             </div>
          </div>
       </div>
    </div>
 </section>

 @endsection