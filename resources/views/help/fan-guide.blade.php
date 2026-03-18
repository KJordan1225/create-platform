@extends('help.layout')

@section('help_title', 'Fan User Guide')
@section('help_subtitle', 'Learn how to discover creators, subscribe, tip, message, comment, and manage your account.')

@section('help_content')
<div class="d-grid gap-4">
    <section>
        <h2 class="h4">Welcome</h2>
        <p class="text-light-emphasis">
            As a fan, you can explore creators, subscribe to premium content, send tips,
            comment on posts, build conversations, and follow creator updates through your feed.
        </p>
    </section>

    <section>
        <h2 class="h4">Creating Your Account</h2>
        <p class="text-light-emphasis">
            Register an account, verify your email if required, log in, and then begin exploring creators.
        </p>
    </section>

    <section>
        <h2 class="h4">Exploring Creators</h2>
        <p class="text-light-emphasis">
            Use the explore page to search for creators and narrow results with filters such as price or tip availability.
        </p>

        <ul class="text-light-emphasis mb-0">
            <li>Search creators by name or profile details</li>
            <li>Filter by maximum monthly price</li>
            <li>Filter creators who allow tips</li>
        </ul>
    </section>

    <section>
        <h2 class="h4">Viewing Creator Profiles</h2>
        <p class="text-light-emphasis">
            Creator pages may show a banner, avatar, bio, subscription price, public posts,
            locked content previews, a subscribe button, a tip button, and a message button.
        </p>
    </section>

    <section>
        <h2 class="h4">Subscribing to a Creator</h2>
        <p class="text-light-emphasis">
            Open a creator profile, click Subscribe, and complete secure payment through Stripe Checkout.
            Once payment is confirmed, locked posts for that creator should become accessible.
        </p>

        <p class="text-light-emphasis mb-0">
            Subscription statuses may include pending, active, incomplete, past due, or canceled.
        </p>
    </section>

    <section>
        <h2 class="h4">Managing Your Subscriptions</h2>
        <p class="text-light-emphasis">
            Your subscriptions page lets you view current and past subscriptions, check status,
            visit creator profiles, and cancel active subscriptions when needed.
        </p>
    </section>

    <section>
        <h2 class="h4">Sending Tips</h2>
        <p class="text-light-emphasis">
            If a creator allows tips, you can send one-time support with an optional message.
            Tips are processed through Stripe and are separate from subscriptions.
        </p>
    </section>

    <section>
        <h2 class="h4">Your Feed</h2>
        <p class="text-light-emphasis">
            The feed shows recent posts from creators you actively subscribe to.
            This is the fastest way to catch up with new content.
        </p>
    </section>

    <section>
        <h2 class="h4">Commenting on Posts</h2>
        <p class="text-light-emphasis">
            Logged-in fans can comment on posts. Keep comments respectful, relevant, and free from spam.
        </p>
    </section>

    <section>
        <h2 class="h4">Messaging Creators</h2>
        <p class="text-light-emphasis">
            You can start a conversation with a creator and continue chatting through your inbox.
            Avoid repeated spam, suspicious links, or harassment.
        </p>
    </section>

    <section>
        <h2 class="h4">Notifications</h2>
        <p class="text-light-emphasis">
            Notifications keep you informed about new messages and other account activity.
            Review them often and mark them as read when appropriate.
        </p>
    </section>

    <section>
        <h2 class="h4">Dashboard Overview</h2>
        <p class="text-light-emphasis">
            Your dashboard may show active subscriptions, recent subscription activity, recent tips,
            and quick links to messages, feed, and creators.
        </p>
    </section>

    <section>
        <h2 class="h4">Account Settings</h2>
        <p class="text-light-emphasis">
            Update your name, username, email, and password from the settings page.
        </p>
    </section>

    <section>
        <h2 class="h4">Common Issues</h2>
        <ul class="text-light-emphasis mb-0">
            <li>Locked content may remain unavailable briefly while payment confirmation completes</li>
            <li>Messaging may be limited by safety checks</li>
            <li>Comments may be blocked if they appear spam-like</li>
            <li>Tips may fail if payment does not complete successfully</li>
        </ul>
    </section>
</div>
@endsection
