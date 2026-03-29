<?php

namespace App\Console\Commands;

use App\Mail\CreatorAccessRevokedMail;
use App\Mail\CreatorCancellationScheduledMail;
use App\Mail\CreatorTrialEndingMail;
use App\Models\CreatorPlatformSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCreatorSubscriptionNotifications extends Command
{
    protected $signature = 'subscriptions:notify-creators';
    protected $description = 'Send creator subscription warning and status emails';

    public function handle(): int
    {
        $this->sendTrialEndingNotices();
        $this->sendCancelScheduledNotices();
        $this->sendRevokedNotices();

        $this->info('Creator subscription notifications processed.');

        return self::SUCCESS;
    }

    protected function sendTrialEndingNotices(): void
    {
        CreatorPlatformSubscription::query()
            ->with(['user', 'plan'])
            ->where('status', 'trialing')
            ->whereNull('trial_ending_notice_sent_at')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->copy()->addDays(3)])
            ->chunkById(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    if (!$subscription->user?->email) {
                        continue;
                    }

                    Mail::to($subscription->user->email)
                        ->queue(new CreatorTrialEndingMail($subscription));

                    $subscription->update([
                        'trial_ending_notice_sent_at' => now(),
                    ]);
                }
            });
    }

    protected function sendCancelScheduledNotices(): void
    {
        CreatorPlatformSubscription::query()
            ->with(['user', 'plan'])
            ->whereNull('cancel_scheduled_notice_sent_at')
            ->whereNotNull('ends_at')
            ->where(function ($query) {
                $query->where('status', 'active')
                      ->orWhere('status', 'trialing');
            })
            ->whereRaw("JSON_EXTRACT(meta, '$.cancel_at_period_end') = true")
            ->chunkById(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    if (!$subscription->user?->email) {
                        continue;
                    }

                    Mail::to($subscription->user->email)
                        ->queue(new CreatorCancellationScheduledMail($subscription));

                    $subscription->update([
                        'cancel_scheduled_notice_sent_at' => now(),
                    ]);
                }
            });
    }

    protected function sendRevokedNotices(): void
    {
        CreatorPlatformSubscription::query()
            ->with(['user', 'plan'])
            ->whereNotNull('revoked_at')
            ->whereNull('revoked_notice_sent_at')
            ->chunkById(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    if (!$subscription->user?->email) {
                        continue;
                    }

                    Mail::to($subscription->user->email)
                        ->queue(new CreatorAccessRevokedMail($subscription));

                    $subscription->update([
                        'revoked_notice_sent_at' => now(),
                    ]);
                }
            });
    }
}
