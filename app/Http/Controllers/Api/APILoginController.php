<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Config;
use JWTAuthException;
use App\Contact;
use App\Customer;
use App\Mail\VerifyMail;
use App\Mail\OrderCreated;
use App\Http\Controllers\Api\BaseController as BaseController;
class APILoginController extends  BaseController
{
   
     /**
         * All Utils instance.
         *
         */
        protected $businessUtil;
        protected $moduleUtil;
       /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
        {
            $this->businessUtil = $businessUtil;
            $this->moduleUtil = $moduleUtil;
            // $this->middleware('auth:customer', ['except' => ['login','register','adminLogin']]);
            config()->set( 'auth.defaults.guard', 'customerapi' );
            \Config::set('jwt.user', 'App\Customer'); 
            \Config::set('auth.providers.users.model', \App\Customer::class);
            $this->customer = new Customer;
        }
    
    public function login(Request $request)
     {
                $validator = Validator::make($request->all(), [
            
                    'email' => 'required|exists:customers,email|email',
                    'password' => 'required',
                
                ]);
                if ($validator->fails())
                {
                    return $this->sendError($validator->errors()->all(), 400);
                }
                
             
                $credentials = $request->only('email', 'password');
                $token = null;
                try {
                    if (! $token = JWTAuth::attempt($credentials)) {
                        return response()->json(['error' => 'invalid_credentials'], 401);
                    }
                   
                    if (empty(Auth::guard('customerapi')->user()->business->is_active)) {
                        \Auth::logout();
                        return $this->sendError( __('lang_v1.business_inactive'), 400);
                    } elseif (Auth::guard('customerapi')->user()->status != 'active') {
                        \Auth::logout();
                        return $this->sendError( __('lang_v1.user_inactive'), 400);
                    } elseif (!Auth::guard('customerapi')->user()->allow_login) {
                        \Auth::logout();
                        return $this->sendError(__('lang_v1.login_not_allowed'), 400);
                    } 
                   
                    return $this->sendResponse([
                        'access_token' => $token,
                        'token_type'   => 'bearer',
                        'business_id' => Auth::guard('customerapi')->user()->business_id,
                        ], 'Success.');

                } catch (JWTAuthException $e) {
                  
                     return $this->sendError($e, 500);
                }
     }

   /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticatedUser(Request $request)
    {
            try {
              
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return $this->sendError(['user_not_found'], 404);
            }

            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return $this->sendError(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return $this->sendError(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return $this->sendError(['token_absent'], $e->getStatusCode());

            }

            return $this->sendResponse($user,'Success');
    }
  
  
    
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        try {
            JWTAuth::invalidate($request->token);
 
            return $this->sendResponse('User logged out successfully','Success');
        } catch (JWTException $exception) {
            return $this->sendError( 'Sorry, the user cannot be logged out', 500);
        }
    }
 
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
    /**
     * Register customer.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|unique:customers|email',
                'username' => 'required|unique:customers',
                'password' => 'required',
                'phone' => 'required|unique:customers',
                'business_id' => 'required|exists:business,id',
                'gender' => 'required|in:female,male',
            ]);
            if ($validator->fails())
            {
                return $this->sendError($validator->errors()->all(), 400);
            }
          
            $user = new Customer;
            $user->first_name = $request->input('name');
            $user->username = $request->input('username');
            $user->phone = $request->input('phone');
            $user->gender = $request->input('gender');
            $user->email = $request->input('email');
            $user->device_token = $request->input('device_token', '');
            $user->password = Hash::make($request->input('password'));
            $user->business_id = $request->input('business_id');
            $user->api_token = str_random(60);
            $user->verify_code =  rand(10000,99999);
            $user->save();
            //if phone is exist 
            $contact= Contact::where('mobile',$request->phone)->first();
            if(!empty($contact)){
                $user->contact_id=$contact->id;
                $user->save();
            }else{
                $finduser=User::where('business_id',$request->input('business_id'))->first();
                if(!empty($finduser)){
                    $contacts_new= new Contact;
                    $contacts_new->business_id=$request->input('business_id');
                    $contacts_new->name=$request->input('name');
                    $contacts_new->first_name=$request->input('name');
                    $contacts_new->type='customer';
                    $contacts_new->mobile=$request->phone;
                    $contacts_new->created_by =$finduser->id;
                    $contacts_new->save();

                    //update user status
                    $user->contact_id=$contacts_new->id;
                    $user->save();
                }else{

                    return $this->sendError('not found main user ', 400);
                }
            }
         
        } catch (\Exception $e) {
          
            return $this->sendError($e->getMessage(), 401);
        }


        return $this->sendResponse($user, 'User retrieved successfully');
    }
    /**
     * Send verify code
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
 
    public  function sendVerifyCode(Request $request)
    {
       
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:customers,email|email',
                'business_id' => 'required|exists:business,id',
            ]);
            if ($validator->fails())
            {
                return $this->sendError($validator->errors()->all(), 400);
            }
           
            $verifyUser =Customer::where([['email',$request->email],['business_id',$request->business_id]])->first();
            if ($verifyUser->isVerify == '1') {
                return $this->sendError('Email Aleardy Verified', 400);
            }
            if ($verifyUser->verify_code == null) {
                    $verifyUser->upadet([
                        'verify_code' => rand(10000,99999),
                    ]);
            }
           
            \Mail::to($verifyUser->email)->send(new VerifyMail($verifyUser));
            return $this->sendResponse('','Verification Code is Sent To Your Email');
           
            } catch (\Exception $e) {

                    return $this->sendError($e->getMessage(), 400);

            }
    }
    /**
     * Verify code 
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public  function VerifyCode(Request $request)
    {
       
        try{
            $validator = Validator::make($request->all(), [
                'code' => 'required|exists:customers,verify_code',
                'business_id' => 'required',
                 'token' => 'required',
            ]);
            if ($validator->fails())
            {
                return $this->sendError($validator->errors()->all(), 400);
            }
           
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->sendError(['user_not_found'], 404);
            }
            if($user->isVerify == '1'){
                return $this->sendError('Your Account has been verified already', 404);
            }
            if($user->verify_code == $request->code){
                $user->isVerify='1';
                $user->save();
                return $this->sendResponse('','Thanks your account is verified Now');
            }
            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return $this->sendError(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return $this->sendError(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return $this->sendError(['token_absent'], $e->getStatusCode());


            } catch (\Exception $e) {

                    return $this->sendError($e->getMessage(), 400);

            }
    }
    /**
     * change password
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public  function changePassword(Request $request)
    {
       
        try{
            $validator = Validator::make($request->all(), [
                'oldpassword' => 'required',
                'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:6',
                'business_id' => 'required',
            ]);
            if ($validator->fails())
            {
                return $this->sendError($validator->errors()->all(), 400);
            }
           
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->sendError(['user_not_found'], 404);
            }
            if (Hash::check($request->oldpassword,$user->password)) {
                $user->password = Hash::make($request->input('password'));
                $user->save();
                return $this->sendResponse('','Thanks your Password is changed successfully');
            }else{
                return $this->sendError(['old password not correct'], 404);
            }
          
          
            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return $this->sendError(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return $this->sendError(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return $this->sendError(['token_absent'], $e->getStatusCode());


            } catch (\Exception $e) {

                    return $this->sendError($e->getMessage(), 400);

            }
    }

  /**
     * change password
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public  function updateprofile(Request $request)
    {
       
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes',
                'email' => 'sometimes|unique:customers|email',
                'phone' => 'sometimes',
                'gender' => 'sometimes|in:female,male',
                'business_id' => 'required',
            ]);
            if ($validator->fails())
            {
                return $this->sendError($validator->errors()->all(), 400);
            }
           
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->sendError(['user_not_found'], 404);
            }else{
                $request->input('name')  ? $user->first_name = $request->input('name'):'';
                $request->input('phone') ? $user->phone = $request->input('phone'):'';
                $request->input('gender')? $user->gender = $request->input('gender'):'';
                $request->input('email') ? $user->email = $request->input('email'):'';
                $user->save();
                $token=auth()->refresh();
                return $this->sendResponse(['user'=>$user,'token'=>$token],'Your account has been updated successfully');
            }
          
            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return $this->sendError(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return $this->sendError(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return $this->sendError(['token_absent'], $e->getStatusCode());


            } catch (\Exception $e) {

                    return $this->sendError($e->getMessage(), 400);

            }
    }
}
