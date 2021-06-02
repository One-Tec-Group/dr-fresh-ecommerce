<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\User;
use App\Contact;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Address;
use App\DeliveryGroups;
use App\Transaction;
class AuthController extends Controller
{
    /**
     * Display the resource.
     * @return Response
     */
    public function customer_profile(Request $request)
    {
        try {
            if(Auth::guard('customer')->check()){
              return view('ecommerce::frontend.profile.profile');
            }else{
                abort(404);
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
            abort(404);
       }
    }
    /**
     * Update of the resource.
     * @return Response
     */
    public function updateProfile(Request $request)
    {
       
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'nullable|max:255',
                'last_name' => 'nullable|max:255',
                'phone' => 'nullable',
                'gender' => 'nullable|in:female,male',
                'email' => 'nullable|email|max:255',
                'password' => 'nullable|min:6|confirmed',
            ]);
    
    
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors()); 
            }

            DB::beginTransaction();
            $user = Customer::find(Auth::guard('customer')->user()->id);
            if($request->first_name != null){$user->first_name = $request->first_name;}
            if($request->last_name != null){$user->last_name = $request->last_name;}
            if($request->phone != null){$user->phone = $request->phone;}
            if($request->gender != null){$user->gender = $request->gender;}
            if($request->password != null){$user->password = bcrypt($request->password);}
            $user->save();

            // //if phone is exist 
            if($request->phone != null){
                if($user->contact_id != null){
                    $contact= Contact::where('id',$user->contact_id)->first();
                    $contact->mobile=$request->phone;
                    $contact->save();
                }else{
                    $finduser=User::where('business_id', config('constants.business_id'))->first();
                    if(!empty($finduser)){
                    $contacts_new= new Contact;
                    $contacts_new->business_id= config('constants.business_id');
                    $contacts_new->name=$user['name'];
                    $contacts_new->first_name=$user['name'];
                    $contacts_new->type='customer';
                    $contacts_new->mobile=$user['phone'];
                    $contacts_new->created_by =$finduser->id;
                    $contacts_new->save();
        
                    //update user status
                    $user->contact_id=$contacts_new->id;
                    $user->save(); 
                }   
                }
           }
          
            DB::commit();

            $notification = array(
                'message' => trans('ecommerce::master.profile_updated'),
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
         
        } catch (\Exception $e) {
            abort(404);
       }
    }

      /**
     * Update or add new address of the resource.
     * @return Response
     */
    public function showAddress(Request $request){
       
        try {
            if(Auth::guard('customer')->check()){
              $addresses = DeliveryGroups::where('business_id', config('constants.business_id'))->get();
              
              $current_address=Auth::guard('customer')->user() ? Auth::guard('customer')->user()->contact ? Auth::guard('customer')->user()->contact->last_addresses : '' : '';
              return view('ecommerce::frontend.profile.address',compact('addresses','current_address'));
            }else{
                abort(404);
            }
        } catch (\Exception $e) {
            
            abort(404);
       }
    }
   

    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_id' => 'required|exists:delivery_groups,id',
            'city' => 'required|string',
            'street_no' => 'required|string',
            'building_number' => 'required|string'
        ]);


        if ($validator->fails()) {
          
             return redirect()->back()->withInput()->withErrors($validator->errors()); 
        }

        
        try {
              
            
            $user = Auth::guard('customer')->user();
            $business_id =  config('constants.business_id');
            $contact_id=$user->contact_id;
            DB::beginTransaction();
            $output =Address::updateOrCreate(
                ['contact_id' =>  $contact_id,
                'business_id' =>  $business_id,
                
               ],
                [
                    'delivery_id' =>  request('delivery_id'),
                    'city' =>  request('city'),
                    'street_no' =>  request('street_no'),
                    'building_number' =>  request('building_number'),
                    'floor' =>  request('floor'),
                    'apartment_number' =>  request('apartment_number'),
                    'special_marque' =>  request('special_marque'),
                    'address_type' => 'website',
                ]
               );

        
            DB::commit();  
            return redirect()->back()->withSuccess('Address Created successfully'); 
           
            } catch (exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
    
                return redirect()->back()->withInput()->withErrors($e->getMessage()); 

           }
      
    
    }

        /**
     * Display order list the resource.
     * @return Response
     */
    public function orderlist(Request $request)
    {
        try {
            if(Auth::guard('customer')->check()){
                $user = Auth::guard('customer')->user();
                $contact_id=$user->contact_id;
                $orderlist =Transaction::where([['business_id', config('constants.business_id')],['contact_id',$contact_id]])->get();
             
              return view('ecommerce::frontend.profile.orderdetails',compact('orderlist'));
            }else{
                abort(404);
            }
        } catch (\Exception $e) {
            abort(404);
       }
    }
        /**
     * Display order list the resource.
     * @return Response
     */
    public function orderdetails(Request $request)
    {
       
        try {
            if(Auth::guard('customer')->check()){
                $user = Auth::guard('customer')->user();
                $contact_id=$user->contact_id;
                $orderlist =Transaction::where([['business_id', config('constants.business_id')],['contact_id',$contact_id],['id',$request->id]])->first();
               $addresses = DeliveryGroups::where('business_id', config('constants.business_id'))->get();
              return view('ecommerce::frontend.profile.singleorder',compact('orderlist','addresses'));
            }else{
                abort(404);
            }
        } catch (\Exception $e) {
            abort(404);
       }
    }

    public function contact()
    {
        return view('ecommerce::frontend.pages.contact');
    }
}
