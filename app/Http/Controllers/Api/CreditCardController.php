<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use App\CreditCard;
use App\Customer;
use JWTAuthException;
use App\DeliveryGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Api\BaseController as BaseController;

class CreditCardController extends BaseController
{
    /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct()
        {
            // $this->middleware('auth:customer', ['except' => ['login','register','adminLogin']]);
            config()->set( 'auth.defaults.guard', 'customerapi' );
            \Config::set('jwt.user', 'App\Customer'); 
            \Config::set('auth.providers.users.model', \App\Customer::class);
            $this->customer = new Customer;
        }
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
             
            if (JWTAuth::parseToken()->check() != true  ) {
                    return $this->sendError(['token expired'], 404);
            }
            $user = JWTAuth::parseToken()->authenticate();

            $business_id = $request->business_id;

            $CreditCard = CreditCard::where('business_id', $business_id)->where('customer_id',$user->id)->get();
            return $this->sendResponse($CreditCard, 'get CreditCard List ');

            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                    return $this->sendError(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return $this->sendError(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return $this->sendError(['token_absent'], $e->getStatusCode());

            }
        
    }


       /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'credit_number' => 'required|integer|digits_between:15,25',
            'month' => 'required|digits:2|between:1,12',
            'year' => 'required|digits:2',
            'saved_secury' => 'required|digits:3',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->all(), 400);
        }

        
        try {
              
            if (JWTAuth::parseToken()->check() != true  ) {
                return $this->sendError(['token expired'], 404);
             }
            $user = JWTAuth::parseToken()->authenticate();
            $business_id = $user->business_id;
            DB::beginTransaction();
            $output =CreditCard::create([
                'name'         => $request->name,
                'month'   => $request->month,
                'credit_number'   => $request->credit_number,
                'year'  => $request->year,
                'saved_secury'    => $request->saved_secury,
                'customer_id'        => $user->id,
                'business_id'       => $business_id,
            ]);

        
            DB::commit();  
            return $this->sendResponse($output, 'CreditCard Created successfully');
           } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return $this->sendError(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return $this->sendError(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return $this->sendError(['token_absent'], $e->getStatusCode());

            } catch (exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
    
                return $this->sendError($e->getMessage(), 401);

           }
      
    
    }

      

           /**
     * update resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_id' => 'required|exists:delivery_groups,id',
            'CreditCard_id' => 'required|exists:CreditCard,id',
        ]);


        if ($validator->fails()) {
            return $this->sendError($validator->errors()->all(), 400);
        }

        
        try {
              
            if (JWTAuth::parseToken()->check() != true  ) {
                return $this->sendError(['token expired'], 404);
             }
            $user = JWTAuth::parseToken()->authenticate();
            DB::beginTransaction();
            $updateCreditCard = CreditCard::where([['id',$request->CreditCard_id],['contact_id',$user->contact_id]])->firstOrFail();
            $request->city != null ? $updateCreditCard->city             = $request->city : '';
            $request->delivery_id != null ? $updateCreditCard->delivery_id      = $request->delivery_id : '';
            $request->street_no != null ? $updateCreditCard->street_no        = $request->street_no : '';
            $request->building_number != null ? $updateCreditCard->building_number  = $request->building_number : '';
            $request->apartment_number != null ? $updateCreditCard->apartment_number = $request->apartment_number : '';
            $request->special_marque != null ? $updateCreditCard->special_marque   = $request->special_marque : '';
            $updateCreditCard->save();
            DB::commit();  
            return $this->sendResponse($updateCreditCard, 'CreditCard Created successfully');
           } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                    return $this->sendError(['token_expired'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                    return $this->sendError(['token_invalid'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                    return $this->sendError(['token_absent'], $e->getStatusCode());
            } catch (exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                return $this->sendError($e->getMessage(), 401);
           }
      
    }
}
