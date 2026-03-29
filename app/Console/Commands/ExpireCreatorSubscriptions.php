<?php

namespace App\Console\Commands;

use App\Models\CreatorPlatformSubscription;
use App\Models\CreatorSubscriptionAudit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireCreatorSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire-creators';
    protected $description = 'Expire stale creator subscriptions, including trials and ended manual access';

    public function handle(): int
    {
        $count = 0;

        CreatorPlatformSubscription::query()
            ->whereNull('expired_at')
            ->where(function ($query) {
                $query
                    ->where(function ($q) {
                        $q->where('status', 'trialing')
                          ->whereNotNull('trial_ends_at')
                          ->where('trial_ends_at', '<', now());
                    })
                    ->orWhere(function ($q) {
                        $q->whereIn('status', ['active', 'trialing'])
                          ->whereNotNull('ends_at')
                          ->where('ends_at', '<', now());
                    });
            })
            ->orderBy('id')
            ->chunkById(100, function ($subscriptions) use (&$count) {
                foreach ($subscriptions as $subscription) {
                    DB::transaction(function () use ($subscription, &$count) {
                        $old = $subscription->only([
                            'status',
                            'is_trial',
                            'trial_ends_at',
                            'ends_at',
                            'expired_at',
                        ]);

                        $action = $subscription->status === 'trialing' ? 'trial_expired' : 'expired';

                        $subscription->update([
                            'status' => 'canceled',
                            'expired_at' => now(),
                            'ends_at' => $subscription->ends_at ?? now(),
                        ]);

                        CreatorSubscriptionAudit::create([
                            'creator_platform_subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                            'action' => $action,
                            'note' => 'Subscription expired automatically by scheduled job.',
                            'old_values' => $old,
                            'new_values' => $subscription->fresh()->only([
                                'status',
                                'is_trial',
                                'trial_ends_at',
                                'ends_at',
                                'expired_at',
                            ]),
                            'meta' => [
                                'source' => 'subscriptions:expire-creators',
                            ],
                        ]);

                        $count++;
                    });
                }
            });

        $this->info("Expired {$count} creator subscription(s).");

        return self::SUCCESS;
    }
}
