<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(){

        $plans = SubscriptionPlan::where('is_active', true)
                              ->orderBy('price')
                              ->get();

        return Inertia::render('Subscription/index', ['plans' => $plans]);
    }

    public function select(Request $request, SubscriptionService $subscriptionService){

        $request->validate([
        'plan_id' => ['required', 'exists:subscription_plans,id'],
    ]);

    $plan = SubscriptionPlan::findOrFail($request->plan_id);

    //free plan
    if (floatval($plan->price) == 0) {
        DB::transaction(function () use ($plan, $subscriptionService) {
            $subscriptionService->store([
                'subs_plan_id'   => $plan->id,
                'starts_at'      => now()->toDateString(),
                'ends_at'        => now()->addMonth()->toDateString(),
                'paid_at'        => now()->toDateString(),
                'payment_method' => 'free',
            ]);
        });

        return redirect()->route('dashboard')
                         ->with('success', 'Selamat datang! Akun kamu sudah aktif.');
    }
    // ─────────────────────────────────────
    // PAID PLAN → arahkan ke pembayaran
    // (service pembayaran belum dibuat)
    // ─────────────────────────────────────
    return redirect()->route('payment.checkout', [
        'plan_id' => $plan->id,
    ]);
    }
}
