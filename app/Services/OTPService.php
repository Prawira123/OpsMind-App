<?php

namespace App\Services;

use App\Models\OTPCode;
use App\Models\User;
use App\Notifications\OtpNotification;

class OTPService extends BaseService
{
    public function __construct()
    {

    }

    public function generate(User $user, string $type){
        OTPCode::where('user_id', $user->id)
        ->where('type', $type)
        ->where('is_used', false)
        ->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);


        $otp = OTPCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => $type,
            'expires_at' => now()->addMinutes(5),
            'is_used' => false
        ]);

        $user->notify(new OtpNotification($otp));

        return $otp;
    }

    public function verify(User $user, string $code, string $type){
        
        $otp = OTPCode::where('user_id', $user->id)
        ->where('code', $code)
        ->where('type', $type)
        ->active()
        ->latest()
        ->first();

        if(!$otp){
            return false;
        }

        $otp->update([
            'is_used' => true
        ]);

        return true;
    }   
}