<?php

namespace Modules\Ecommerce\Http\Controllers\CustomerAuth;

use App\User;
use Socialite;
use App\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Hesto\MultiAuth\Traits\LogsoutGuard;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, LogsoutGuard {
        LogsoutGuard::logout insteadof AuthenticatesUsers;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/ecommerce';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->web = Auth::guard('customer');
        $this->middleware('customer.guest', ['except' => 'customlogout']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {

        return Auth::guard('customer');
    }


    public function redirectToProvider(string $provider)
    {
        
        try {
               
            return Socialite::driver($provider)->redirect();

        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function handleProviderCallback(string $provider)
    {
       
        try {
            $data = Socialite::driver($provider)->stateless()->user();
            
            return $this->handleSocialUser($provider, $data);
        } catch (\Exception $e) {
            return redirect('/')->with([
                'message' =>'Login with ' . ucfirst($provider) . ' failed. Please try again.' 
              , 'alert-type' => 'error'
             ]);
        }
    }

    public function handleSocialUser(string $provider, object $data)
    {

        $user = Customer::where([
            "social->{$provider}->id" => $data->id,
        ])->first();
        if (!$user) {
            $user = Customer::where([
                'email' => $data->email,
            ])->first();
        }
        if (!$user) {
            return $this->createUserWithSocialData($provider, $data);
        }
        $social = $user->social;
        $social[$provider] = [
            'id' => $data->id,
            'token' => $data->token
        ];
        $user->social = $social;
        $user->save();
        return $this->socialLogin($user);
    }

    public function createUserWithSocialData(string $provider, object $data)
    {
        try {
            $user = new Customer;
            $user->name = $data->name;
            $user->username = $data->name;
            $user->email = $data->email;
            $user->phone = $data->phone;
            $user->social = [
                $provider => [
                    'id' => $data->id,
                    'token' => $data->token,
                ],
            ];
            // Check support verify or not
            // if ($user instanceof MustVerifyEmail) {
            //     $user->markEmailAsVerified();
            // }
            $user->save();
            return $this->socialLogin($user);
        } catch (Exception $e) {
            return redirect('login')->withErrors(['authentication_deny' => 'Login with ' . ucfirst($provider) . ' failed. Please try again.']);
        }
    }

    public function socialLogin(Customer $user)
    {
        auth()->loginUsingId($user->id);
        return redirect($this->redirectTo);
    }


    public function customlogout()
    {
        if ($this->web->check() == true) {
            $this->web->logout();
            return redirect()->intended('/ecommerce');
        }
        return redirect()->intended('/ecommerce');
    }

}
