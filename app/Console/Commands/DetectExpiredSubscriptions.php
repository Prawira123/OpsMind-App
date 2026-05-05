<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DetectExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:detect-expired';
    protected $description = 'Detect expired paid subscriptions and revert tenants to free plan';

    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        try {
            $results = $this->subscriptionService->detectExpiredSubscriptions();

            if (empty($results)) {
                $this->info('No expired subscriptions found.');
                return 0;
            }

            $this->info('Found ' . count($results) . ' expired subscription(s):');
            foreach ($results as $result) {
                $this->line("  - Tenant #{$result['tenant_id']}: Reverted to free plan (subscription #{$result['new_subscription_id']})");
                Log::info('Subscription expired and reverted to free', $result);
            }

            $this->info('Done. Expired subscriptions have been processed.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Failed to detect expired subscriptions: ' . $e->getMessage());
            return 1;
        }
    }
}
