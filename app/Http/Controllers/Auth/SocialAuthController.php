<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider){
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback($provider){
        $socialiteUser = Socialite::driver($provider)->stateless()->user();
        $user = User::where('email', $socialiteUser->getEmail())->first();

        if($user){
            $user->update([
                'provider_id' => $socialiteUser->getId(),
                'provider' => $provider,
            ]);
        }else{
            $user = User::create([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'provider_id' => $socialiteUser->getId(),
                'provider' => $provider,
                'password' => bcrypt(Str::random(16))
            ]);
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
