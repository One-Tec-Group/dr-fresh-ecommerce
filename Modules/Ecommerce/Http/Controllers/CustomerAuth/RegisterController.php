<?php

namespace Modules\Ecommerce\Http\Controllers\CustomerAuth;

use App\User;
use Validator;
use App\Contact;
use App\Customer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/ecommerce';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('customer.guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'phone' => 'required|unique:customers',
            'gender' => 'required|in:female,male',
            'email' => 'required|email|max:255|unique:customers',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Customer
     */
    protected function create(array $data)
    {
        try {
            DB::beginTransaction();
         
        $user = new Customer;
        $user->first_name = $data['name'];
        $user->username = $data['name'].''.rand(10000,99999);
        $user->phone = $data['phone'];
        $user->gender = $data['gender'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->business_id =  config('constants.business_id');
        $user->verify_code =  rand(10000,99999);
        $user->save();
      
        //if phone is exist 
        $contact= Contact::where('mobile',$data['phone'])->first();
        if(!empty($contact)){
            $user->contact_id=$contact->id;
            $user->save();
        }else{
            $finduser=User::where('business_id', config('constants.business_id'))->first();
            if(!empty($finduser)){
            $contacts_new= new Contact;
            $contacts_new->business_id= config('constants.business_id');
            $contacts_new->name=$data['name'];
            $contacts_new->first_name=$data['name'];
            $contacts_new->type='customer';
            $contacts_new->mobile=$data['phone'];
            $contacts_new->created_by =$finduser->id;
            $contacts_new->save();

            //update user status
            $user->contact_id=$contacts_new->id;
            $user->save(); 
           }   
        }
        // dd($user,'one1');
        DB::commit();  
        return $user;
      } catch (exception $e) {
         
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

        return $this->sendError($e->getMessage(), 401);

    }
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('customer.auth.register');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('customer');
    }
}
