<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HandleInertiaRequestCacheObserver
{
    protected function clearInertiRequestCache(){
        if(Auth::check() && Auth::user()->tenant_id){
            $userId = Auth::user()->id; 
            $keys = [
                "user_{$userId}:profile:getDataProfile",
            ];

            foreach($keys as $key){
                Cache::forget($key);
            }
        }
    }

    public function created(): void
    {
        $this->clearInertiRequestCache();
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(): void
    {
        $this->clearInertiRequestCache();
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(): void
    {
        $this->clearInertiRequestCache();
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(): void
    {
        $this->clearInertiRequestCache();
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(): void
    {
        $this->clearInertiRequestCache();
    }
}
