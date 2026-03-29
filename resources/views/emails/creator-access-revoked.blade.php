@php
    $user = $subscription->user;
    $plan = $subscription->plan;
@endphp

<p>Hello {{ $user->name }},</p>

<p>Your creator access for {{ $plan->name ?? 'your creator plan' }} has been revoked.</p>

@if ($subscription->admin_note)
    <p><strong>Note:</strong> {{ $subscription->admin_note }}</p>
@endif

<p>This means you may no longer be able to create posts or upload creator media unless access is restored.</p>

<p>
    <a href="{{ route('creator.billing.subscribe') }}">Review your billing page</a>
</p>

<p>Thank you.</p>
