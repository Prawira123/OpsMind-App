<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Notifications\SuscriptionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function notification(Request $request)
    {
        $payload = $request->getContent();
        $notification = new Notification();

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status;

        Log::info("Midtrans Notification received: $orderId - $transaction");

        $subscription = Subscription::withoutGlobalScopes()
            ->where('order_id', $orderId)
            ->first();

        if (!$subscription) {
            Log::error("Subscription not found for order ID: $orderId");
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        if ($transaction == 'settlement' || ($transaction == 'capture' && $fraud == 'accept')) {
            DB::transaction(function () use ($subscription, $type) {
                // Activate subscription
                $subscription->update([
                    'status' => 'active',
                    'paid_at' => now(),
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                    'payment_method' => $type,
                ]);

                // Update Tenant
                $tenant = Tenant::findOrFail($subscription->tenant_id);
                $tenant->update(['subs_id' => $subscription->id]);

                // Create Invoice
                $invoice = Invoice::create([
                    'tenant_id' => $subscription->tenant_id,
                    'created_by' => $subscription->user_id,
                    'number' => 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(4)),
                    'status' => 'paid',
                    'issue_date' => now(),
                    'due_date' => now(),
                    'total' => $subscription->plan->price,
                    'subtotal' => $subscription->plan->price,
                    'tax' => 0,
                    'public_token' => Str::random(32),
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'name' => 'Subscription: ' . $subscription->plan->name,
                    'quantity' => 1,
                    'price' => $subscription->plan->price,
                    'total' => $subscription->plan->price,
                ]);

                // Notify User
                $user = $subscription->tenant->user;
                if ($user) {
                    $user->notify(new SuscriptionNotification($user, $subscription->plan));
                }
            });
        } elseif ($transaction == 'pending') {
            $subscription->update(['status' => 'pending']);
        } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            $subscription->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'Notification received']);
    }
}
