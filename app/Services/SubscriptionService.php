<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class SubscriptionService extends BaseService
{
    public function __construct(public Subscription $subscription, public SubscriptionPlan $subscriptionPlan)
    {
    }
    public function store(array $data){

        $order_id = 'SBS-'.uniqid(); 

        // Hapus subscription lama untuk tenant ini jika ada
        $this->subscription->where('tenant_id', Auth::user()->tenant_id)->delete();

        $subscription = $this->subscription->create([
            'user_id' => Auth::user()->id,
            'tenant_id' => Auth::user()->tenant_id,
            'subs_plan_id' => $data['subs_plan_id'],
            'order_id' => $order_id,
            'status' => 'active',
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'paid_at' => $data['paid_at'],
            'payment_method' => $data['payment_method'],
        ]); 

        // Update subs_id di tabel tenants
        Tenant::where('id', Auth::user()->tenant_id)
          ->firstOrFail()
          ->update(['subs_id' => $subscription->id]);

        return $subscription;
    }
}