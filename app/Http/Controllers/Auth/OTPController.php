<?php

namespace App\Http\Controllers\Auth;

use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Controller;


class OTPController extends Controller
{
    public function create(string $type){
        return Inertia::render('Auth/Otp', [
            'type'  => $type,
            'email' => Auth::user()->email,
        ]);
    }

    public function store(string $type, OTPService $service, Request $request){
        
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'Kode OTP wajib diisi',
            'code.digits'   => 'Kode OTP harus 6 digit angka',
        ]);

        $user = Auth::user();

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
        }

        return redirect()->route('dashboard')
                        ->with('success', 'Email berhasil diverifikasi!');  
    }

    public function resendCode(OTPService $service, string $type ){
        $service->generate(Auth::user(), $type);

        return back()->with('success', 'Kode OTP berhasil dikirim ulang');
    }
}
