<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DashboardCacheObserver
{
    /**
     * Clear dashboard cache for the current tenant.
     */
    protected function clearDashboardCache(): void
    {
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenantId = Auth::user()->tenant_id;
            
            // Define all dashboard-related cache keys for this tenant
            $keys = [
                "tenant_{$tenantId}:dashboard:getIncomeTotal",
                "tenant_{$tenantId}:dashboard:getTransactionThisMonth",
                "tenant_{$tenantId}:dashboard:getInvoicePending",
                "tenant_{$tenantId}:dashboard:getTotalBalance",
                "tenant_{$tenantId}:dashboard:getTopClient",
                "tenant_{$tenantId}:dashboard:getRecentTransaction",
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    public function created(): void
    {
        $this->clearDashboardCache();
    }

    public function updated(): void
    {
        $this->clearDashboardCache();
    }

    public function deleted(): void
    {
        $this->clearDashboardCache();
    }

    public function restored(): void
    {
        $this->clearDashboardCache();
    }

    public function forceDeleted(): void
    {
        $this->clearDashboardCache();
    }
}
