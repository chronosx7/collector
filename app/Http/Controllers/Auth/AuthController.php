<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Validator;
use App\Factories\ActivationFactory;
use App\Models\User;
use Auth;
use Socialite;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    private $auth_providers = array('facebook');
    
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(ActivationFactory $activationFactory)
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        $this->activationFactory = $activationFactory;
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
            'name' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data){
        if(array_key_exists('activated', $data) === false || $data['activated'] === ''){
            $data['activated'] = false;
        }
        if(array_key_exists('uses_social', $data) === false || $data['uses_social'] === ''){
            $data['uses_social'] = false;
        }
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'activated' => $data['activated'],
            'uses_social' => $data['uses_social'],
        ]);
    }

    public function redirectToProvider($provider){
        if(in_array($provider, $this->auth_providers)){
            return Socialite::driver($provider)->redirect();
        }
        else{
            return redirect('/login');
        }
    }
    
    public function handleProviderCallback($provider){
        
        $social_user = Socialite::driver($provider)->user();
        
        $user_model = new User();
        $local_user = User::where('email', $social_user->email)->first();
        if($local_user == null){
            // create user using random name and password
            if(!property_exists($social_user, 'name')){
                $social_user->name = get_random_name();
            }
            else{
                $social_user->name = str_replace(' ', '_', $social_user->name);
                $social_user->name = strtolower($social_user->name);
                if($user_model->user_name_exists($social_user->name)){
                    $social_user->name = $user_model->get_random_name($social_user->name);
                }
            }
            $local_user = $this->create(array(
                'name' => $social_user->name,
                'email' => $social_user->email,
                'password' => $user_model->get_random_password(),
                'uses_social' => true,
                'activated' => true,
            ));
        }
        Auth::login($local_user);
        //$redirect_url = '/games/yugioh/cards/';
        //return redirect($redirect_url);
        return redirect()->intended('/');
    }
    
    public function register(Request $request){
        $validator = $this->validator($request->all());
        
        if($validator->fails()){
            $this->throwValidationException($request, $validator);
        }
        $user = $this->create($request->all());
        $this->activationFactory->sendActivationMail($user);
        
        return redirect('/login')->with('activationStatus', true);
    }
    
    public function activateUser($token){
        if($user = $this->activationFactory->activateUser($token)){
            auth()->login($user);
            return redirect($this->redirectPath());
        }
        abort(404);
    }
    
    public function authenticated(Request $request, $user){
        if(!$user->activated){
            $this->activationFactory->sendActivationMail($user);
            auth()->logout();
            return back()->with('activationWarning', true);
        }
        return redirect()->intended($this->redirectPath());
    }
    
}









