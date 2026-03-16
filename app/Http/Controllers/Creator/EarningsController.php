<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\PayoutReport;
use App\Models\Plf_subscription;
use App\Models\Tip;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningsController extends Controller
{
    public function index(Request $request): View
    {
        $creator = $request->user();

        $subscriptionRevenue = Plf_subscription::query()
            ->where('creator_id', $creator->id)
            ->where('status', 'active')
            ->sum('amount');

        $tipRevenue = Tip::query()
            ->where('creator_id', $creator->id)
            ->where('status', 'succeeded')
            ->sum('amount');

        $recentTips = Tip::query()
            ->where('creator_id', $creator->id)
            ->where('status', 'succeeded')
            ->with('fan')
            ->latest()
            ->paginate(15, ['*'], 'tips_page');

        $payoutReports = PayoutReport::query()
            ->where('creator_id', $creator->id)
            ->latest('period_end')
            ->paginate(12, ['*'], 'payouts_page');

        $stats = [
            'subscription_revenue' => $subscriptionRevenue,
            'tip_revenue' => $tipRevenue,
            'gross_revenue' => $subscriptionRevenue + $tipRevenue,
        ];

        return view('creator.earnings.index', compact(
            'creator',
            'stats',
            'recentTips',
            'payoutReports'
        ));
    }
}
