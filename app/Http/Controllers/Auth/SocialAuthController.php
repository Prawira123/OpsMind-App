<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Services\AuthService;

class SocialAuthController extends Controller
{   
    protected $allowedProviders = ['google', 'github'];
    public function redirect($provider){

        if(!in_array($provider, $this->allowedProviders)){
            abort(404);
        }
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback($provider, AuthService $service){
        if(!in_array($provider, $this->allowedProviders)){
            abort(404);
        }   

        $user = $service->handleSocialLogin($provider);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
