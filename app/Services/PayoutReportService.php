<?php

namespace App\Services;

use App\Models\PayoutReport;
use App\Models\Plf_subscription;
use App\Models\Tip;
use App\Models\User;
use Illuminate\Support\Carbon;

class PayoutReportService
{
    public function generateForCreator(User $creator, Carbon $periodStart, Carbon $periodEnd): PayoutReport
    {
        $subscriptionRevenue = Plf_subscription::query()
            ->where('creator_id', $creator->id)
            ->where('status', 'active')
            ->whereBetween('created_at', [$periodStart->copy()->startOfDay(), $periodEnd->copy()->endOfDay()])
            ->sum('amount');

        $tipRevenue = Tip::query()
            ->where('creator_id', $creator->id)
            ->where('status', 'succeeded')
            ->whereBetween('created_at', [$periodStart->copy()->startOfDay(), $periodEnd->copy()->endOfDay()])
            ->sum('amount');

        $grossTotal = $subscriptionRevenue + $tipRevenue;

        $platformFeeTotal = round($grossTotal * 0.20, 2); // example: 20% platform fee
        $estimatedProcessorFeeTotal = round($grossTotal * 0.035, 2); // estimate only
        $netCreatorAmount = max(0, $grossTotal - $platformFeeTotal - $estimatedProcessorFeeTotal);

        return PayoutReport::updateOrCreate(
            [
                'creator_id' => $creator->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
            ],
            [
                'gross_subscription_revenue' => $subscriptionRevenue,
                'gross_tip_revenue' => $tipRevenue,
                'gross_total' => $grossTotal,
                'platform_fee_total' => $platformFeeTotal,
                'estimated_processor_fee_total' => $estimatedProcessorFeeTotal,
                'net_creator_amount' => $netCreatorAmount,
                'status' => 'pending',
            ]
        );
    }
}
