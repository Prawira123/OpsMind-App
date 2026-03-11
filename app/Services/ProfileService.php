<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileService extends BaseService
{
    public function __construct()
    {
    }

    public function updateUser(array $data){
        $user_id = Auth::user()->id;
        $user = User::where('id', $user_id)->first();

        if(!$user){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email']
        ]);

        return $user;
    }

    public function updateTenant(array $data){
        $tenant_id = Auth::user()->tenant_id;
        $tenant = Tenant::where('id', $tenant_id)->first();

        if(!$tenant){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $tenant->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'currency' => $data['currency'],
            'timezone' => $data['timezone'],
            'logo' => $data['logo'],
        ]);

        return $tenant;
    }

    public function update2FA(array $data){
        $userid = Auth::user()->id;
        $user = User::find($userid);

        if(!$user){
            abort(403, 'Kamu tidak Punya Akses');
        }   

        $user->update([
            'two_factor_enabled' => $data['enabled'],
        ]);
    }  

    public function updatePassword(array $data){
        $userid = Auth::user()->id;
        $user = User::find($userid);

        if(!$user){
            abort(403, 'Kamu tidak Punya Akses');
        }

        if(!$user->password == $data['current_password']){
            abort(403, 'Password Lama Salah');
        }

        $user->update([
            'password' => bcrypt($data['password']),
        ]);
    }
}