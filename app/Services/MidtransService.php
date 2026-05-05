<?php

namespace App\Services;

use Midtrans\Config;
use Illuminate\Support\Facades\Storage;

class MidtransService extends BaseService
{
    public function __construct()
    {
        $this->configure();
    }

    public function configure()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken($params){
        return \Midtrans\Snap::getSnapToken($params);
    }

    public function createQrisCharge($params)
    {
        $charge = \Midtrans\CoreApi::charge([
            'payment_type' => 'qris',
            'transaction_details' => $params['transaction_details'],
            'customer_details' => $params['customer_details'],
            'item_details' => $params['item_details'] ?? [],
        ]);

        // Simpan respon JSON ke storage
        $this->saveTransactionToStorage($charge);

        return $charge;
    }

    protected function saveTransactionToStorage($response)
    {
        $identifier = $response['transaction_id'] ?? $response['order_id'] ?? 'unknown';
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "midtrans_transactions/{$identifier}-{$timestamp}.json";

        Storage::disk('local')->put($filename, json_encode($response, JSON_PRETTY_PRINT));
    }
}