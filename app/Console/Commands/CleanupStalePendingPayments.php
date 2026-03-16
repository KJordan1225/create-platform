<?php

namespace App\Console\Commands;

use App\Models\Plf_subscription;
use App\Models\Tip;
use Illuminate\Console\Command;

class CleanupStalePendingPayments extends Command
{
    protected $signature = 'app:cleanup-stale-pending-payments';
    protected $description = 'Mark stale pending subscriptions and tips as failed/expired';

    public function handle(): int
    {
        $subscriptionCount = Plf_subscription::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(12))
            ->update([
                'status' => 'incomplete',
            ]);

        $tipCount = Tip::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(12))
            ->update([
                'status' => 'failed',
            ]);

        $this->info("Updated {$subscriptionCount} stale subscriptions.");
        $this->info("Updated {$tipCount} stale tips.");

        return self::SUCCESS;
    }
}
