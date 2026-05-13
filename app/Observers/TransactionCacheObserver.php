<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TransactionCacheObserver
{

    protected function clearTransactionCache(Transaction $transaction){
        if(Auth::check() && Auth::user()->tenant_id){
            $tenantId = Auth::user()->tenant_id;
          
            if($transaction->tenant_id === $tenantId){
                $key = "tenant_{$tenantId}:transaction:getTransactionData";
                Cache::forget($key);
            }
        }
    }
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $this->clearTransactionCache($transaction);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $this->clearTransactionCache($transaction);
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $this->clearTransactionCache($transaction);
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        $this->clearTransactionCache($transaction);
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        $this->clearTransactionCache($transaction);
    }
}
