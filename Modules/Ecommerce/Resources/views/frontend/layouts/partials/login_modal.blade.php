<div class="modal fade login-modal-main" id="bd-example-modal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
       <div class="modal-content">
          <div class="modal-body">
             <div class="login-modal">
                <div class="row">
                   <div class="col-lg-6 pad-right-0">
                      <div class="login-modal-left" style="background: rgba(0, 0, 0, 0) url('{{asset('frontend/images/logo.png')}}')  scroll center center">
                      </div>
                   </div>
                   <div class="col-lg-6 pad-left-0">
                      <button type="button" class="close close-top-right" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true"><i class="mdi mdi-close"></i></span>
                      <span class="sr-only">@lang('ecommerce::master.close')</span>
                      </button>
                      <div class="login-modal-right">
                         <!-- Tab panes -->
                         <div class="tab-content">
                            <div class="tab-pane active" id="login" role="tabpanel">
                                  <form role="form" method="POST" action="{{ route('customer.savelogin') }}">
                                    {{ csrf_field() }}
                                  <h5 class="heading-design-h5">@lang('ecommerce::master.Login_to_your_account')</h5>
                                  <fieldset class="form-group">
                                     <label>@lang('ecommerce::master.Enter_Email')</label>
                                     <input type="email" class="form-control" name="email" placeholder="@lang('ecommerce::master.replace_email')">
                                  </fieldset>
                                  <fieldset class="form-group">
                                     <label>@lang('ecommerce::master.Enter_Password')</label>
                                     <input type="password" class="form-control" name="password" placeholder="********">
                                  </fieldset>
                                  <fieldset class="form-group">
                                     <button type="submit" class="btn btn-lg btn-secondary btn-block">@lang('ecommerce::master.Enter_to_your_account')</button>
                                  </fieldset>
                                  <div class="custom-control custom-checkbox">
                                     <input type="checkbox" class="custom-control-input" id="customCheck1"  name="remember">
                                     <label class="custom-control-label" for="customCheck1">@lang('ecommerce::master.Remember_me')</label>
                                    </div>
                                    <div class="login-with-sites text-center">
                                       <p>@lang('ecommerce::master.orsociallogin')</p>
                          
                                       <a href="{{ url('customer/login/google') }}" class="btn-google login-icons btn-lg"> @lang('ecommerce::master.Google')</a>
                                       <a href="{{ url('customer/login/facebook') }}" class="btn-facebook login-icons btn-lg"> @lang('ecommerce::master.Facebook')</a>
                                       {{-- <a class="btn-twitter login-icons btn-lg"><i class="mdi mdi-twitter"></i> @lang('ecommerce::master.Twitter')</button> --}}
                                    </div>
                                 </form>
                               </div>
                               <div class="tab-pane" id="register" role="tabpanel">
                                 <form role="form" method="post" action="{{ route('customer.store_register') }}">
                                    {{ csrf_field() }}
                                  <h5 class="heading-design-h5">@lang('ecommerce::master.register_now')</h5>
                                  <fieldset class="form-group">
                                     <label>@lang('ecommerce::master.name')</label>
                                     <input type="text" class="form-control" name="name" placeholder="@lang('ecommerce::master.replace_name')">
                                  </fieldset>
                                  <fieldset class="form-group">
                                     <label>@lang('ecommerce::master.phone')</label>
                                     <input type="text" class="form-control" name="phone" placeholder="@lang('ecommerce::master.replace_phone')">
                                  </fieldset>
                                  <fieldset class="form-group">
                                     <label>@lang('ecommerce::master.Enter_Email')</label>
                                     <input type="email" class="form-control" name="email" placeholder="@lang('ecommerce::master.replace_email')">
                                     @if ($errors->has('email'))
                                     <span class="help-block">
                                         <strong>{{ $errors->first('email') }}</strong>
                                     </span>
                                    @endif
                                  </fieldset>
                                  <fieldset class="form-group">
                                     <label>@lang('ecommerce::master.Enter_Password')</label>
                                     <input type="password" class="form-control" name="password" placeholder="********">
                                  
                                       @if ($errors->has('password'))
                                       <span class="help-block">
                                             <strong>{{ $errors->first('password') }}</strong>
                                       </span>
                                       @endif   
                                  </fieldset>
                                  <fieldset class="form-group">
                                     <label>@lang('ecommerce::master.Enter_confirm_Password') </label>
                                     <input type="password" class="form-control"  name="password_confirmation" placeholder="********">
                                     @if ($errors->has('password_confirmation'))
                                     <span class="help-block">
                                         <strong>{{ $errors->first('password_confirmation') }}</strong>
                                     </span>
                                    @endif
                                  </fieldset>
                                  <fieldset class="form-group">
                                     <label>@lang('ecommerce::master.gender') </label>
                                     <select name="gender" class="select2 form-control border-form-control">
                                        <option value="male">@lang('ecommerce::master.male')</option>
                                        <option value="female">@lang('ecommerce::master.female')</option>
                                     </select>
                                  </fieldset>
                                  <fieldset class="form-group">
                                     <button type="submit" class="btn btn-lg btn-secondary btn-block">@lang('ecommerce::master.create_account')</button>
                                  </fieldset>
                                  
                                 </form>
                               </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="text-center login-footer-tab">
                               <ul class="nav nav-tabs" role="tablist">
                                  <li class="nav-item">
                                     <a class="nav-link active" data-toggle="tab" href="#login" role="tab"><i class="mdi mdi-lock"></i> @lang('ecommerce::master.login')</a>
                                  </li>
                                  <li class="nav-item">
                                     <a class="nav-link" data-toggle="tab" href="#register" role="tab"><i class="mdi mdi-pencil"></i> @lang('ecommerce::master.register')</a>
                                  </li>
                               </ul>
                            </div>
                            <div class="clearfix"></div>
                         </div>
                     
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>