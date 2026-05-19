<?php

namespace App\Observers;

use App\Events\Dashboard\GetDataFinancial;
use App\Events\Dashboard\GetDataInvoicePending;
use App\Events\Dashboard\GetDataRecentTransaction;
use App\Events\Dashboard\GetDataTopClient;
use App\Events\Dashboard\GetDataUserOnline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardCacheObserver
{
    /**
     * Clear dashboard cache for the current tenant.
     */
    protected function clearDashboardCache($model = null): void
    {
        $tenantId = null;
        if (Auth::check()) {
            $tenantId = Auth::user()->tenant_id;
        } elseif ($model && isset($model->tenant_id)) {
            $tenantId = $model->tenant_id;
        }

        Log::info("DashboardCacheObserver clearDashboardCache called!", ['tenantId' => $tenantId]);

        if ($tenantId) {
            try {
                // Define all dashboard-related cache keys for this tenant
                $keys = [
                    "tenant_{$tenantId}:dashboard:getDataFinancial",
                    "tenant_{$tenantId}:dashboard:getInvoicePending",
                    "tenant_{$tenantId}:dashboard:getTopClient",
                    "tenant_{$tenantId}:dashboard:getRecentTransaction",
                    "tenant_{$tenantId}:dashboard:getUserOnline",
                ];

                foreach ($keys as $key) {
                    Cache::forget($key);
                }

                Log::info("DashboardCacheObserver: Cache cleared successfully.", ['tenantId' => $tenantId]);

                // // Dispatch event to broadcast the new data!
                event(new GetDataFinancial($tenantId));
                event(new GetDataInvoicePending($tenantId));
                event(new GetDataTopClient($tenantId));
                event(new GetDataRecentTransaction($tenantId));
                event(new GetDataUserOnline($tenantId));
                
                Log::info("DashboardCacheObserver: All dashboard events dispatched successfully.");
            } catch (\Exception $e) {
                Log::error("DashboardCacheObserver: Error occurred in clearDashboardCache: " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }
        }
    }

    public function created($model): void
    {
        $this->clearDashboardCache($model);
    }

    public function updated($model): void
    {
        $this->clearDashboardCache($model);
    }

    public function deleted($model): void
    {
        $this->clearDashboardCache($model);
    }

    public function restored($model): void
    {
        $this->clearDashboardCache($model);
    }

    public function forceDeleted($model): void
    {
        $this->clearDashboardCache($model);
    }
}
