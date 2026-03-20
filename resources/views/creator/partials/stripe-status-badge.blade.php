@php
    $status = $creator->stripe_onboarding_status ?? 'pending';

    $map = [
        'connected' => [
            'class' => 'bg-success-subtle text-success border border-success-subtle',
            'label' => 'Connected',
        ],
        'needs_action' => [
            'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'label' => 'Needs Action',
        ],
        'pending' => [
            'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'label' => 'Pending',
        ],
    ];

    $badge = $map[$status] ?? $map['pending'];
@endphp

<span class="badge rounded-pill px-3 py-2 {{ $badge['class'] }}">
    Stripe: {{ $badge['label'] }}
</span>
