<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Services\OTPService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request, AuthService $service, OTPService $generateOTP): RedirectResponse
    {
        $user = $service->register([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'business_name' => $request->business_name,
        ]);

        // refresh user agar tenant_id dan semua relasi terbaru tersedia
        $user->refresh();

        event(new Registered($user));

        Auth::login($user); 

        $generateOTP->generate($user, 'email_verification');

        return redirect()->route('otp.verify', ['type' => 'email_verification']);
    }
}
