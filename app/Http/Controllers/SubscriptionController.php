<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Notifications\SuscriptionNotification;
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

        $currentSubscription = \App\Models\Subscription::with('plan')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'active')
            ->first();

        return Inertia::render('Subscription/index', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
        ]);
    }

    public function select(Request $request, SubscriptionService $subscriptionService){

    $request->validate([
        'plan_id' => ['required', 'exists:subscription_plans,id'],
        'paid_at' => ['required', 'date'],
        'ends_at' => ['required', 'date', 'after:paid_at'],
        'expire_current' => ['boolean'],
    ]);

    $plan = SubscriptionPlan::findOrFail($request->plan_id);

    // Expire current subscription if requested
    if ($request->expire_current) {
        $subscriptionService->expireCurrentSubscription(auth()->user()->tenant_id);
    }

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

        $user = auth()->user();
        $user->notify(new SuscriptionNotification($user, $plan));

        return redirect()->route('dashboard')
                         ->with('success', 'Selamat datang! Akun kamu sudah aktif.');
    }

    return redirect()->route('payment.checkout', [
        'plan_id' => $plan->id,
        'paid_at' => $request->paid_at,
        'ends_at' => $request->ends_at,
    ]);
    }

    public function checkout(Request $request, SubscriptionService $subscriptionService)
    {
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $user = auth()->user();

        $data = [
            'subs_plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'amount' => $plan->price,
            'first_name' => $user->name,
            'last_name' => '',
            'email' => $user->email,
            'paid_at' => $request->paid_at,
            'ends_at' => $request->ends_at,
        ];

        $result = $subscriptionService->subscribe($data);

        return Inertia::render('Subscription/Checkout', [
            'plan' => $plan,
            'snap_token' => $result['snap_token'],
            'midtrans_client_key' => config('services.midtrans.client_key'),
        ]);
    }

    public function checkStatus(Request $request, SubscriptionService $subscriptionService)
    {
        $orderId = $request->get('order_id');
        $status = $subscriptionService->checkMidtransStatus($orderId);

        return response()->json($status);
    }
}
