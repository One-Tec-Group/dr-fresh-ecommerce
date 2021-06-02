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
use App\Http\Controllers\Api\BaseController as BaseController;
class AdminLoginController extends BaseController
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
            config()->set( 'auth.defaults.guard', 'api' );
            \Config::set('jwt.user', 'App\User'); 
            \Config::set('auth.providers.users.model', \App\User::class);
            $this->user = new User;
        }
    
    public function login(Request $request)
     {
                $validator = Validator::make($request->all(), [
            
                    'email' => 'required|exists:users,email|email',
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
                   
                    if (empty(Auth::guard('api')->user()->business->is_active)) {
                        \Auth::logout();
                        return $this->sendError( __('lang_v1.business_inactive'), 400);
                    } elseif (Auth::guard('api')->user()->status != 'active') {
                        \Auth::logout();
                        return $this->sendError( __('lang_v1.user_inactive'), 400);
                    } elseif (!Auth::guard('api')->user()->allow_login) {
                        \Auth::logout();
                        return $this->sendError(__('lang_v1.login_not_allowed'), 400);
                    } 
                   
                    return $this->sendResponse([
                        'access_token' => $token,
                        'token_type'   => 'bearer',
                        'business_id' => Auth::guard('api')->user()->business_id,
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
}
