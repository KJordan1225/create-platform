@extends('help.layout')

@section('help_title', 'Creator User Guide')
@section('help_subtitle', 'Learn how to build your creator profile, publish content, receive subscriptions, and manage earnings.')

@section('help_content')
<div class="d-grid gap-4">
    <section>
        <h2 class="h4">Welcome</h2>
        <p class="text-light-emphasis">
            Welcome to the creator side of the platform. This application lets you build a public creator profile,
            publish free and subscriber-only content, receive monthly subscriptions, accept tips, message fans,
            and monitor earnings.
        </p>
    </section>

    <section>
        <h2 class="h4">Getting Started</h2>
        <div class="d-grid gap-3">
            <div>
                <h3 class="h6">Create your account</h3>
                <p class="text-light-emphasis mb-0">
                    Register a normal user account, then log in and use the creator application option from your dashboard.
                </p>
            </div>

            <div>
                <h3 class="h6">Submit your creator application</h3>
                <p class="text-light-emphasis mb-0">
                    Complete your display name, public slug, bio, monthly price, avatar, banner, and tip preference.
                </p>
            </div>

            <div>
                <h3 class="h6">Approval process</h3>
                <p class="text-light-emphasis mb-0">
                    Once approved, your public profile becomes active, your creator dashboard becomes available,
                    and fans can subscribe to you and tip you.
                </p>
            </div>
        </div>
    </section>

    <section>
        <h2 class="h4">Creator Dashboard</h2>
        <p class="text-light-emphasis">
            Your dashboard is your main control center. It may show your post totals, subscriber count,
            recent tips, recent subscribers, inbox access, earnings, and quick links to profile and post management.
        </p>
    </section>

    <section>
        <h2 class="h4">Editing Your Profile</h2>
        <p class="text-light-emphasis">
            Your profile is what fans see publicly. Keep it polished and easy to understand.
        </p>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="border rounded-4 p-3 h-100">
                    <h3 class="h6">Profile fields</h3>
                    <ul class="text-light-emphasis mb-0">
                        <li>Display name</li>
                        <li>Slug</li>
                        <li>Bio</li>
                        <li>Monthly subscription price</li>
                        <li>Avatar</li>
                        <li>Banner</li>
                        <li>Tip preference</li>
                    </ul>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="border rounded-4 p-3 h-100">
                    <h3 class="h6">Important note</h3>
                    <p class="text-light-emphasis mb-0">
                        If your monthly price changes, the platform may sync a new Stripe recurring price in the background.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section>
        <h2 class="h4">Creating Posts</h2>
        <p class="text-light-emphasis">
            Posts are the core of your creator content. You can add captions, upload media,
            and choose whether the post is public or locked for subscribers only.
        </p>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="border rounded-4 p-3 h-100">
                    <h3 class="h6">Public posts</h3>
                    <p class="text-light-emphasis mb-0">
                        Visible to anyone visiting your profile. Use these as previews, announcements, or promotional content.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="border rounded-4 p-3 h-100">
                    <h3 class="h6">Locked posts</h3>
                    <p class="text-light-emphasis mb-0">
                        Fully visible only to active subscribers, you, and administrators.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section>
        <h2 class="h4">Managing Posts</h2>
        <p class="text-light-emphasis">
            From your posts area, you can edit captions, upload or remove media, switch locked or public status,
            publish or unpublish posts, and delete content when needed.
        </p>
    </section>

    <section>
        <h2 class="h4">Subscriptions</h2>
        <p class="text-light-emphasis">
            Fans subscribe through Stripe Checkout. Once payment is confirmed, your local subscription records
            update and the fan gains access to locked posts.
        </p>
        <p class="text-light-emphasis mb-0">
            Subscription statuses may include pending, active, incomplete, past due, or canceled.
        </p>
    </section>

    <section>
        <h2 class="h4">Tips</h2>
        <p class="text-light-emphasis">
            If enabled, fans can send one-time tips along with optional messages.
            Tips are separate from subscriptions and are shown in your earnings and recent activity areas.
        </p>
    </section>

    <section>
        <h2 class="h4">Messaging Fans</h2>
        <p class="text-light-emphasis">
            Your inbox allows private messaging with fans. Keep messages clear, respectful, and free of spam-like behavior.
        </p>
    </section>

    <section>
        <h2 class="h4">Notifications</h2>
        <p class="text-light-emphasis">
            Notifications can alert you to new messages, new subscribers, approval changes, and other important account activity.
        </p>
    </section>

    <section>
        <h2 class="h4">Earnings and Payout Reports</h2>
        <p class="text-light-emphasis">
            Your earnings area may show subscription revenue, tip revenue, gross totals, and payout reports.
            Payout reporting helps you understand what has been earned and what may be pending for payout processing.
        </p>
    </section>

    <section>
        <h2 class="h4">Safety and Abuse Controls</h2>
        <p class="text-light-emphasis">
            The platform may flag suspicious messaging, comment spam, or unusual tip behavior.
            Avoid repeated copy-paste messages, suspicious links, and off-platform payment requests.
        </p>
    </section>

    <section>
        <h2 class="h4">Account Settings</h2>
        <p class="text-light-emphasis">
            Use account settings to update your name, username, email address, and password.
            Keep your email current so you can receive important notifications.
        </p>
    </section>

    <section>
        <h2 class="h4">Best Practices</h2>
        <ul class="text-light-emphasis mb-0">
            <li>Keep your profile polished</li>
            <li>Post consistently</li>
            <li>Mix public previews with premium content</li>
            <li>Respond respectfully to fans</li>
            <li>Monitor your earnings and subscriber activity</li>
        </ul>
    </section>
</div>
@endsection
