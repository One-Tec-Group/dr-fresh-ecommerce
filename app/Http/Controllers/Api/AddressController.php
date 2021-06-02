<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use App\Address;
use App\Customer;
use JWTAuthException;
use App\DeliveryGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Api\BaseController as BaseController;

class AddressController extends BaseController
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

            $addresses = Address::where('business_id', $business_id)->where('contact_id',$user->contact_id)->get();
            return $this->sendResponse($addresses, 'get Address List ');

            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                    return $this->sendError(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return $this->sendError(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return $this->sendError(['token_absent'], $e->getStatusCode());

            }
        
    }

    
       /**
         * required  created resource in storage.
         *
         * @param  \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function create(Request $request){
            $validator = Validator::make($request->all(), [
                'business_id' => 'required|exists:business,id',
            ]);
    
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->all(), 400);
            }
            $business_id = $request->business_id;
            $addresses = DeliveryGroups::where('business_id', $business_id)->get();
            return $this->sendResponse(['delivery_places'=>$addresses], 'get delivery List ');
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
            'delivery_id' => 'required|exists:delivery_groups,id',
            'city' => 'required|string',
            'street_no' => 'required|string',
            'building_number' => 'required|string'
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
            $output =Address::create([
                'city'              => $request->city,
                'delivery_id'       => $request->delivery_id,
                'street_no'         => $request->street_no,
                'building_number'   => $request->building_number,
                'apartment_number'  => $request->apartment_number,
                'special_marque'    => $request->special_marque,
                'business_id'       => $business_id,
                'contact_id'        => $user->contact_id,
            ]);

        
            DB::commit();  
            return $this->sendResponse($output, 'Address Created successfully');
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
         * Required date to edit resource in storage.
         *
         * @param  \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function edit(Request $request){
            $validator = Validator::make($request->all(), [
                'address_id' => 'required|exists:addresses,id',
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
                $delivery_places = DeliveryGroups::where('business_id', $business_id)->get();
                $addresses = Address::where([['id',$request->address_id],['contact_id',$user->contact_id]])->get();
                return $this->sendResponse(['delivery_places'=>$delivery_places,'address'=>$addresses], 'edit address');
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
            'address_id' => 'required|exists:addresses,id',
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
            $updateaddress = Address::where([['id',$request->address_id],['contact_id',$user->contact_id]])->firstOrFail();
            $request->city != null ? $updateaddress->city             = $request->city : '';
            $request->delivery_id != null ? $updateaddress->delivery_id      = $request->delivery_id : '';
            $request->street_no != null ? $updateaddress->street_no        = $request->street_no : '';
            $request->building_number != null ? $updateaddress->building_number  = $request->building_number : '';
            $request->apartment_number != null ? $updateaddress->apartment_number = $request->apartment_number : '';
            $request->special_marque != null ? $updateaddress->special_marque   = $request->special_marque : '';
            $updateaddress->save();
            DB::commit();  
            return $this->sendResponse($updateaddress, 'Address Created successfully');
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
