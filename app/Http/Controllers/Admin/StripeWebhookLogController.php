<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StripeWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StripeWebhookLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));
        $processed = $request->get('processed');

        $logs = StripeWebhookEvent::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('event_type', 'like', "%{$search}%")
                      ->orWhere('event_id', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($processed !== null && $processed !== '', function ($query) use ($processed) {
                $query->where('processed', (bool) $processed);
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.stripe-webhook-logs.index', compact('logs', 'search', 'processed'));
    }

    public function show(StripeWebhookEvent $stripeWebhookLog): View
    {
        return view('admin.stripe-webhook-logs.show', [
            'log' => $stripeWebhookLog,
        ]);
    }
}
