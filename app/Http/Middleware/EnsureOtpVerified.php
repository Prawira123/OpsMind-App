<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureOtpVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::user();

        if(!$user){
            return redirect()->route('login');
        }

        if(!$user->email_verified_at){
            return redirect()->route('otp.verify', ['type' => 'email_verification']);
        }
        return $next($request);
    }
}
