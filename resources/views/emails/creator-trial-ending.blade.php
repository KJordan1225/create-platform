@php
    $user = $subscription->user;
    $plan = $subscription->plan;
@endphp

<p>Hello {{ $user->name }},</p>

<p>Your creator trial for {{ $plan->name ?? 'your creator plan' }} is ending soon.</p>

@if ($subscription->trial_ends_at)
    <p>Your trial ends on <strong>{{ $subscription->trial_ends_at->format('F j, Y') }}</strong>.</p>
@endif

<p>To avoid interruption in your ability to create posts and upload media, please review your billing settings.</p>

<p>
    <a href="{{ route('creator.billing.subscribe') }}">Manage your creator subscription</a>
</p>

<p>Thank you.</p>
