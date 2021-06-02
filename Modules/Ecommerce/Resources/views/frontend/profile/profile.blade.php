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
                         <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action active"><i aria-hidden="true" class="mdi mdi-account-outline"></i>  @lang('ecommerce::master.My_Profile')</a>
                         <a href="{{ route('customer.address') }}" class="list-group-item list-group-item-action"><i aria-hidden="true" class="mdi mdi-map-marker-circle"></i>  @lang('ecommerce::master.My_Address')</a>
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
                                @lang('ecommerce::master.My_Profile')
                            </h5>
                         </div>
                         <form method="POST" action="{{ route('customer.updateProfile')}}">
                           @csrf
                            <div class="row">
                               <div class="col-sm-6">
                                  <div class="form-group">
                                     <label class="control-label">@lang('ecommerce::master.first_name') <span class="required">*</span></label>
                                     <input class="form-control border-form-control" name="first_name" value="{{ Auth::guard('customer')->check()?Auth::guard('customer')->user()->first_name:'' }}" placeholder="@lang('ecommerce::master.first_name')" type="text">
                                     @if ($errors->has('first_name'))
                                     <span class="help-block">
                                         <strong>{{ $errors->first('first_name') }}</strong>
                                     </span>
                                    @endif
                                    </div>
                               </div>
                               <div class="col-sm-6">
                                  <div class="form-group">
                                     <label class="control-label">@lang('ecommerce::master.last_name') <span class="required">*</span></label>
                                     <input class="form-control border-form-control" name="last_name" value="{{ Auth::guard('customer')->check()?Auth::guard('customer')->user()->last_name:'' }}" placeholder="@lang('ecommerce::master.last_name')" type="text">
                                     @if ($errors->has('last_name'))
                                     <span class="help-block">
                                         <strong>{{ $errors->first('last_name') }}</strong>
                                     </span>
                                    @endif
                                    </div>
                               </div>
                            </div>
                            <div class="row">
                               <div class="col-sm-6">
                                  <div class="form-group">
                                     <label class="control-label">@lang('ecommerce::master.phone') <span class="required">*</span></label>
                                     <input class="form-control border-form-control" name="phone" value="{{ Auth::guard('customer')->check()?Auth::guard('customer')->user()->phone:'' }}" placeholder="@lang('ecommerce::master.replace_phone')" type="number">
                                     @if ($errors->has('phone'))
                                     <span class="help-block">
                                         <strong>{{ $errors->first('phone') }}</strong>
                                     </span>
                                    @endif
                                    </div>
                               </div>
                               <div class="col-sm-6">
                                  <div class="form-group">
                                     <label class="control-label">@lang('ecommerce::master.email')<span class="required">*</span></label>
                                     <input class="form-control border-form-control " name="email" value="{{ Auth::guard('customer')->check()?Auth::guard('customer')->user()->email:'' }}" placeholder="@lang('ecommerce::master.replace_email')" type="text" disabled="" type="email">
                                     @if ($errors->has('email'))
                                     <span class="help-block">
                                         <strong>{{ $errors->first('email') }}</strong>
                                     </span>
                                    @endif
                                    </div>
                               </div>
                            </div>
                         
                            <div class="row">
                               <div class="col-sm-12">
                                  <div class="form-group">
                                     <label class="control-label">@lang('ecommerce::master.gender') <span class="required">*</span></label>
                                     <select  class="select2 form-control border-form-control" name="gender" >
                                        <option value="male" {{  Auth::guard('customer')->check()? (Auth::guard('customer')->user()->gender == 'male' ? 'selected':'' ) : ''}} >@lang('ecommerce::master.male')</option>
                                        <option value="female" {{  Auth::guard('customer')->check()? (Auth::guard('customer')->user()->gender == 'female' ? 'selected':'') : '' }} >@lang('ecommerce::master.female')</option>
                                     </select>
                                     @if ($errors->has('gender'))
                                     <span class="help-block">
                                         <strong>{{ $errors->first('gender') }}</strong>
                                     </span>
                                    @endif
                                  </div>
                               </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-6">
                              <div class="form-group">
                                 <label>@lang('ecommerce::master.Enter_Password')</label>
                                 <input type="password" class="form-control" name="password" placeholder="********">
                              
                                   @if ($errors->has('password'))
                                   <span class="help-block">
                                         <strong>{{ $errors->first('password') }}</strong>
                                   </span>
                                   @endif   
                              </div>
                              </div>
                              <div class="col-sm-6">
                              <div class="form-group">
                                 <label>@lang('ecommerce::master.Enter_confirm_Password') </label>
                                 <input type="password" class="form-control"  name="password_confirmation" placeholder="********">
                                 @if ($errors->has('password_confirmation'))
                                 <span class="help-block">
                                     <strong>{{ $errors->first('password_confirmation') }}</strong>
                                 </span>
                                @endif
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