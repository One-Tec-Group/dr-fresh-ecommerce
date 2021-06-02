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
                         <a href="{{ route('customer.address') }}" class="list-group-item list-group-item-action active"><i aria-hidden="true" class="mdi mdi-map-marker-circle"></i>  @lang('ecommerce::master.My_Address')</a>
                         <a href="{{ route('customer.orderlist') }}" class="list-group-item list-group-item-action"><i aria-hidden="true" class="mdi mdi-format-list-bulleted"></i>@lang('ecommerce::master.Order_List')</a> 
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
                              @lang('ecommerce::master.Contact_Address')
                           </h5>
                        </div>
                        <form  action="{{ route('customer.updateAddress') }}" method="POST" >
                          @csrf
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
                                             
                                             <input class="form-control" placeholder="@lang('ecommerce::master.city')" name="city" type="text" id="city" value="{{ $current_address->city ?? '' }}">
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
                                            
                                          <select class="form-control" name="delivery_id" id="delivery_id" >
                                                 <option value="">@lang('ecommerce::master.no_one')</option>
                                                 @foreach($addresses as $address)
                                                 <option value="{{ $address->id }}" {{ $current_address->delivery_id ?( $current_address->delivery_id == $address->id ? 'selected': '') :'' }} >{{ $address->name }}</option>   
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
                                             
                                             <input class="form-control" placeholder=" @lang('ecommerce::master.street')" name="street_no" type="text" id="street_no" value="{{ $current_address->street_no ?? '' }}">
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
                                             <input class="form-control" placeholder=" @lang('ecommerce::master.building')" name="building_number" type="text" id="building_number"  value="{{ $current_address->building_number ?? '' }}">
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
                                            
                                             <input class="form-control" placeholder="@lang('ecommerce::master.floor')" name="floor" type="text" id="floor"  value="{{ $current_address->floor ?? '' }}">
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
                                             <input class="form-control" placeholder="@lang('ecommerce::master.appartment')" name="apartment_number" type="text" id="apartment_number"  value="{{ $current_address->apartment_number ?? '' }}">
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
                                             <input class="form-control" placeholder="@lang('ecommerce::master.mark')" name="special_marque" type="text" id="special_marque" value="{{ $current_address->special_marque ?? '' }}">
                                             @if ($errors->has('special_marque'))
                                           <span class="help-block">
                                               <strong>{{ $errors->first('special_marque') }}</strong>
                                           </span>
                                          @endif
                                         </div>
                                     </div>
                                 </div>
                             </div>
                           <div class="row">
                              <div class="col-sm-12 text-right">
                                 <button type="button" class="btn btn-danger btn-lg"> @lang('ecommerce::master.Cencel') </button>
                                 <button type="submit" class="btn btn-success btn-lg"> @lang('ecommerce::master.Save_Changes') </button>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
             </div>
          </div>
       </div>
    </div>
 </section>

 @endsection