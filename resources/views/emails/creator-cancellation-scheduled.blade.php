@php
    $user = $subscription->user;
    $plan = $subscription->plan;
@endphp

<p>Hello {{ $user->name }},</p>

<p>Your creator subscription for {{ $plan->name ?? 'your creator plan' }} has been scheduled to cancel at the end of the current billing period.</p>

@if ($subscription->ends_at)
    <p>Your posting access is expected to remain active until <strong>{{ $subscription->ends_at->format('F j, Y') }}</strong>.</p>
@endif

<p>If you want to keep your creator access active, you can reactivate your subscription before the end date.</p>

<p>
    <a href="{{ route('creator.billing.subscribe') }}">Manage your creator subscription</a>
</p>

<p>Thank you.</p>
