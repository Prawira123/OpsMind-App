<?php

namespace App\Observers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InvoiceCacheObserver
{

    protected function clearInvoiceCache(Invoice $invoice){
        if(Auth::check() && Auth::user()->tenant_id){
            $tenantId = Auth::user()->tenant_id;
           
            if($invoice->tenant_id === $tenantId){
                $keys = [
                    "tenant_{$tenantId}:invoice:getInvoiceData",
                    "invoice_{$invoice->id}:detail",
                ];

                foreach($keys as $key){
                    Cache::forget($key);
                }
            }
        }
    }
    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        $this->clearInvoiceCache($invoice);
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        $this->clearInvoiceCache($invoice);
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        $this->clearInvoiceCache($invoice);
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        $this->clearInvoiceCache($invoice);
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
        $this->clearInvoiceCache($invoice);
    }
}
