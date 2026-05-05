<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Midtrans\Snap;
use Midtrans\Transaction;

class SubscriptionService extends BaseService
{
    public function __construct(
        public Subscription $subscription,
        public SubscriptionPlan $subscriptionPlan,
        protected MidtransService $midtransService
    )
    {
    }

    public function subscribe(array $data){
        $order_id = 'SBS-'.uniqid();

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $data['amount'],
            ],
            'customer_details' => [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
            ],
            'item_details' => [
                [
                    'id' => $data['subs_plan_id'],
                    'price' => $data['amount'],
                    'quantity' => 1,
                    'name' => $data['plan_name'],
                ],
            ],
        ];

        $snapToken = $this->midtransService->getSnapToken($params);

        // Create or update pending subscription
        $subscription = $this->subscription->updateOrCreate(
            ['tenant_id' => Auth::user()->tenant_id, 'status' => 'pending'],
            [
                'user_id' => Auth::id(),
                'subs_plan_id' => $data['subs_plan_id'],
                'order_id' => $order_id,
                'snap_token' => $snapToken,
                'payment_method' => 'midtrans',
                'starts_at' => now(),
                'status' => 'active',
                'ends_at' => $data['ends_at'],
                'paid_at' => $data['paid_at'],
            ]
        );

        Tenant::where('id', Auth::user()->tenant_id)
          ->firstOrFail()
          ->update(['subs_id' => $subscription->id]);

        return [
            'snap_token' => $snapToken,
            'order_id' => $order_id
        ];
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

    public function checkMidtransStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return [
                'success' => true,
                'data' => $status
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function detectExpiredSubscriptions()
    {
        // Ambil semua subscription berbayar yang sudah lewat masa aktif
        $expiredSubscriptions = $this->subscription
            ->where('payment_method', '!=', 'free')
            ->where('status', 'active')
            ->where('ends_at', '<', now())
            ->get();

        $results = [];

        foreach ($expiredSubscriptions as $subscription) {
            // Update status subscription lama jadi expired
            $subscription->update(['status' => 'expired']);

            // Cari subscription free untuk tenant ini
            $freeSubscription = $this->subscription
                ->where('tenant_id', $subscription->tenant_id)
                ->whereHas('plan', function ($query) {
                    $query->where('price', 0);
                })
                ->first();

            if ($freeSubscription) {
                // Update tenant supaya pakai subscription free
                Tenant::where('id', $subscription->tenant_id)
                    ->update(['subs_id' => $freeSubscription->id]);

                // Kirim notifikasi ke user pembuat tenant (user_id)
                $user = \App\Models\User::find($subscription->user_id);
                if ($user) {
                    $user->notify(new \App\Notifications\SuscriptionNotification($user, $freeSubscription->plan));
                }

                $results[] = [
                    'tenant_id' => $subscription->tenant_id,
                    'old_subscription_id' => $subscription->id,
                    'new_subscription_id' => $freeSubscription->id,
                    'status' => 'reverted_to_free',
                    'notification_sent_to' => $user ? $user->email : null,
                ];
            }
        }

        return $results;
    }

    public function expireCurrentSubscription($tenantId)
    {
        $subscription = $this->subscription
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'expired']);
        }
    }
}