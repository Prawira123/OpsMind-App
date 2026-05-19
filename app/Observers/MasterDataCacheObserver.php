<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MasterDataCacheObserver
{
    /**
     * Clear master data cache for the current tenant.
     */
    protected function clearMasterDataCache($model): void
    {
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenantId = Auth::user()->tenant_id;
            $class = class_basename($model);
            $keys = [];

            switch ($class) {
                case 'Client':
                    $keys = [
                        "tenant_{$tenantId}:client:all",
                        "tenant_{$tenantId}:client:all_with_tenant",
                    ];
                    break;
                case 'Category':
                    $keys = [
                        "tenant_{$tenantId}:category:all",
                        "tenant_{$tenantId}:category:all_with_tenant",
                    ];
                    break;
                case 'Account':
                    $keys = [
                        "tenant_{$tenantId}:account:all",
                        "tenant_{$tenantId}:account:all_with_tenant",
                    ];
                    break;
                case 'ChartOfAccount':
                    $keys = [
                        "tenant_{$tenantId}:chart_of_account:all",
                        "tenant_{$tenantId}:chart_of_account:all_with_account_type",
                        "tenant_{$tenantId}:chart_of_account:all_with_tenant",
                    ];
                    break;
            }

            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    public function created($model): void
    {
        $this->clearMasterDataCache($model);
    }

    public function updated($model): void
    {
        $this->clearMasterDataCache($model);
    }

    public function deleted($model): void
    {
        $this->clearMasterDataCache($model);
    }

    public function restored($model): void
    {
        $this->clearMasterDataCache($model);
    }

    public function forceDeleted($model): void
    {
        $this->clearMasterDataCache($model);
    }
}
