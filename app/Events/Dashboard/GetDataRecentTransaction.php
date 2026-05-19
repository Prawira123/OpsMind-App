<?php

namespace App\Events\Dashboard;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class getDataRecentTransaction implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $tenantId;
    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("tenant.{$this->tenantId}"),
        ];
    }
    
    public function broadcastAs(): string{
        return 'data-recent-transactions';
    }

    public function broadcastWith(): array{
        return [
            'recentTransactions' => app(\App\Services\DashboardService::class)->getRecentTransaction($this->tenantId),
        ];
    }
}
