<?php

namespace App\Http\Controllers\Auth;

use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Models\User;


class OTPController extends Controller
{
    public function create(string $type){

        if($type === 'forgot_password'){
            $email = session('forgot_password_email');
        }elseif($type === 'two_factor'){
            $email = session('two_factor_email');
        }else{
            $email = Auth::user()->email;
        }

        return Inertia::render('Auth/Otp', [
            'type'  => $type,
            'email' => $email,
        ]);
    }

    public function store(string $type, OTPService $service, Request $request){
        
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'Kode OTP wajib diisi',
            'code.digits'   => 'Kode OTP harus 6 digit angka',
        ]);

        if($type === 'forgot_password'){
            $email = session('forgot_password_email');
            $user = User::where('email', $email)->first();
        }elseif($type === 'two_factor'){
            $user = User::find(session('two_factor_user_id'));
        }else{
            $user = Auth::user();
        }

        $verified = $service->verify($user, request('code'), $type);

        if (!$verified) {
            return back()->withErrors([
                'code' => 'Kode OTP tidak valid atau sudah expired'
            ]);
        }

        if ($type === 'email_verification') {
            $user->update([
                'email_verified_at' => now()
            ]);
            return redirect()->route('dashboard')
                         ->with('success', 'Email berhasil diverifikasi!');
        }

        if($type == 'forgot_password'){

            session(['forgot_password_verified' => true]);
            Password::sendResetLink(
                ['email' => $user->email]
            );

            return redirect()->route('login')
                         ->with('status', 'Link reset password telah dikirim ke email kamu');
        }

        if($type == 'two_factor'){
            $user = User::find(session('two_factor_user_id'));
            Auth::login($user);
            session()->forget('two_factor_user_id');
            return redirect()->route('dashboard')
                        ->with('success', 'Login berhasil!');  
        }

        return redirect()->route('dashboard')
                        ->with('success', 'Email berhasil diverifikasi!');  
    }

    public function resendCode(OTPService $service, string $type ){
        
        if($type === 'forgot_password'){
            $email = session('forgot_password_email');
            $user = User::where('email', $email)->first();
        }else{
            $user = Auth::user();
        }
        
        $service->generate($user, $type);

        return back()->with('success', 'Kode OTP berhasil dikirim ulang');
    }
}
