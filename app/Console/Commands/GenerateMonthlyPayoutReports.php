<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PayoutReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateMonthlyPayoutReports extends Command
{
    protected $signature = 'app:generate-monthly-payout-reports';
    protected $description = 'Generate monthly payout reports for approved active creators';

    public function handle(PayoutReportService $payoutReportService): int
    {
        $periodStart = now()->subMonthNoOverflow()->startOfMonth();
        $periodEnd = now()->subMonthNoOverflow()->endOfMonth();

        $creators = User::query()
            ->where('role', 'creator')
            ->where('is_creator', true)
            ->where('is_active', true)
            ->whereNotNull('creator_approved_at')
            ->with('creatorProfile')
            ->get();

        foreach ($creators as $creator) {
            $payoutReportService->generateForCreator($creator, $periodStart, $periodEnd);
        }

        $this->info('Monthly payout reports generated successfully.');

        return self::SUCCESS;
    }
}
