<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Socialite;
use App\User;
use JWTAuth;

class SocialiteController extends Controller 
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider($provider){
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider){
        $user = Socialite::driver($provider)->user();

        $authUser = User::where('provider_id',$user->id)->first();

        if(User::where('email',$user->email)->count() != 0){

            $target = User::where('email',$user->email)->first();
            $jwt_token = JWTAuth::fromUser($target);
            
            return response()->json([
                'success' => true,
                'message' => 'Usuário autenticado com sucesso!',
                'token' => $jwt_token
            ]);

            header("Location: ".env('PROJECT_URL')."?token=".$jwt_token);
            die(); 
        }
        
        $user = new User([
            'name' => $user->name,
            'email' => $user->email,
        ]);
        $user->save(); //PARA REGISTRAR O ID

        //INSERIR DADOS DO SOCIALITE AQUI
        $user->provider = $provider;
        $user->provider_id = $user->id;
        $user->save();

        $jwt_token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Usuário cadastrado com sucesso!',
            'token' => $jwt_token
        ]);

        header("Location: ".env('PROJECT_URL')."?token=".$jwt_token);
        die();
    }


}
