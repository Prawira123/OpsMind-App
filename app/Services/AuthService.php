<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthService extends BaseService
{
    public function __construct(private TenantService $tenantService)
    {
    }

    public function register(array $data){
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    

        $this->tenantService->createForUser($user, $data['business_name']);

        return $user;
    }

    public function handleSocialLogin( $provider){
        try{
            $socialiteUser = Socialite::driver($provider)->stateless()->user();
        }catch(\Exception $e){
            throw new \Exception('OAuth ' . $provider . ' gagal: ' . $e->getMessage());
        }

        $socialAccount = SocialAccount::where('provider', $provider)
                                      ->where('provider_id', $socialiteUser->getId())
                                      ->first();

        // Jika sudah pernah login via provider ini → langsung return user
        if ($socialAccount) {
            return $socialAccount->user;
        }
        
        $user = User::where('email', $socialiteUser->getEmail())->first();

        if($user){
            if (!$user->tenant_id) {
                $this->tenantService->createForUser(
                    $user,
                    $socialiteUser->getName() . "'s Business"
                );
            }
        }else{
            $user = User::create([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'password' => bcrypt(Str::random(16)),
                'email_verified_at' => now()
            ]);

            if (!$user->tenant_id) {
                $this->tenantService->createForUser(
                    $user,
                    $socialiteUser->getName() . "'s Business"
                );
            }
        }

        SocialAccount::firstOrCreate([
            'user_id'     => $user->id,
            'provider'    => $provider,
            'provider_id' => $socialiteUser->getId(),
            'avatar' => $socialiteUser->getAvatar() ?? null,
        ]);

        return $user;
    }
}
