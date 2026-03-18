@extends('help.layout')

@section('help_title', 'Admin Operations Guide')
@section('help_subtitle', 'Learn how to review creators, moderate content, manage reports, monitor analytics, and keep operations healthy.')

@section('help_content')
<div class="d-grid gap-4">
    <section>
        <h2 class="h4">Welcome</h2>
        <p class="text-light-emphasis">
            Admins manage creator approvals, moderation, reports, analytics, payouts, and operational health.
        </p>
    </section>

    <section>
        <h2 class="h4">Core Admin Responsibilities</h2>
        <ul class="text-light-emphasis">
            <li>Review creator applications</li>
            <li>Approve or suspend creator accounts</li>
            <li>Moderate posts and comments</li>
            <li>Review reports submitted by users</li>
            <li>Monitor analytics and messaging activity</li>
            <li>Check payout reports</li>
            <li>Monitor queues, mail, scheduler, and payment health</li>
        </ul>
    </section>

    <section>
        <h2 class="h4">Admin Dashboard</h2>
        <p class="text-light-emphasis">
            The admin dashboard provides a high-level view of users, creators, pending approvals,
            active subscriptions, and tip activity.
        </p>
    </section>

    <section>
        <h2 class="h4">Reviewing Creator Applications</h2>
        <p class="text-light-emphasis">
            When reviewing a creator, check profile completeness, public-facing quality, pricing,
            image quality, and overall readiness for the platform.
        </p>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="border rounded-4 p-3 h-100">
                    <h3 class="h6">Approve</h3>
                    <p class="text-light-emphasis mb-0">
                        Makes the creator public, enables creator access, and triggers Stripe pricing sync.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="border rounded-4 p-3 h-100">
                    <h3 class="h6">Suspend</h3>
                    <p class="text-light-emphasis mb-0">
                        Disables the creator from public listings and hides their public creator presence when necessary.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section>
        <h2 class="h4">Creator Management</h2>
        <p class="text-light-emphasis">
            Admin creator pages allow you to review profile details, posts, subscription activity,
            creator state, and moderation actions.
        </p>
    </section>

    <section>
        <h2 class="h4">Report Review</h2>
        <p class="text-light-emphasis">
            Reports may target creators, posts, or comments. Each report can be reviewed and marked resolved or dismissed.
        </p>

        <ul class="text-light-emphasis mb-0">
            <li>Inspect the report reason and details</li>
            <li>Inspect the target content or creator</li>
            <li>Take moderation action if needed</li>
            <li>Resolve or dismiss the report</li>
        </ul>
    </section>

    <section>
        <h2 class="h4">Content Moderation</h2>
        <p class="text-light-emphasis">
            Admins may hide posts, publish hidden posts, delete posts, hide comments, restore comments, or delete comments.
        </p>
        <p class="text-light-emphasis mb-0">
            When in doubt, hiding content is often safer than permanently deleting it during active review.
        </p>
    </section>

    <section>
        <h2 class="h4">Analytics</h2>
        <p class="text-light-emphasis">
            Analytics help monitor platform growth, creator success, conversation volume, message activity,
            subscriptions, and tip totals.
        </p>

        <ul class="text-light-emphasis">
            <li>Total users</li>
            <li>Total creators and fans</li>
            <li>Posts and comments</li>
            <li>Messages and conversations</li>
            <li>Active subscriptions</li>
            <li>Top creators by tips</li>
            <li>Top creators by subscribers</li>
        </ul>
    </section>

    <section>
        <h2 class="h4">Messaging and Abuse Controls</h2>
        <p class="text-light-emphasis">
            The platform may flag suspicious messages, spam comments, or unusual tipping patterns.
            These controls are intended to protect creators, fans, and the platform.
        </p>
    </section>

    <section>
        <h2 class="h4">Queue and Mail Operations</h2>
        <p class="text-light-emphasis">
            Queued jobs may handle creator approval mail, subscriber notifications, and other operational events.
            If emails fail, check queue workers, failed jobs, SMTP settings, and mail credentials.
        </p>
    </section>

    <section>
        <h2 class="h4">Scheduled Jobs</h2>
        <p class="text-light-emphasis">
            Scheduled commands may clean stale pending payments, clear old notifications,
            and generate monthly payout reports. Confirm that the scheduler is running correctly.
        </p>
    </section>

    <section>
        <h2 class="h4">Payout Reports</h2>
        <p class="text-light-emphasis">
            Payout reports summarize creator revenue for a period, including gross revenue,
            platform fees, estimated processor fees, and net creator amounts.
        </p>
    </section>

    <section>
        <h2 class="h4">Payment Operations</h2>
        <p class="text-light-emphasis">
            Stripe handles subscriptions and tips. Admins should verify webhook configuration,
            recurring price sync, and payment status alignment between Stripe and local records.
        </p>
    </section>

+    <section>
        <h2 class="h4">Operational Health Checklist</h2>
        <ul class="text-light-emphasis">
            <li>Site online and accessible</li>
            <li>Registration and login working</li>
            <li>Creator approval flow working</li>
            <li>Queue worker running</li>
            <li>Scheduler running</li>
            <li>Stripe webhook functioning</li>
            <li>Mail delivery working</li>
            <li>Uploads and storage working</li>
            <li>Analytics loading correctly</li>
        </ul>
    </section>

    <section>
        <h2 class="h4">Best Practices</h2>
        <ul class="text-light-emphasis mb-0">
            <li>Apply moderation consistently</li>
            <li>Review reports carefully</li>
            <li>Verify payment state before manual changes</li>
            <li>Monitor queue and scheduler health regularly</li>
            <li>Prefer hiding over deleting during active investigations</li>
        </ul>
    </section>
</div>
@endsection
